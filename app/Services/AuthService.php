<?php

namespace App\Services;

use App\Events\SuspiciousLoginDetected;
use App\Repositories\UserLogRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserSessionRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $userRepo;
    protected $userLogRepo;
    protected $sessionRepo;
    protected $deviceService;
    protected $geoService;
    protected $accountSharingDetectionService;

    public function __construct(
        UserRepository $userRepo,
        UserLogRepository $userLogRepo,
        UserSessionRepository $sessionRepo,
        DeviceService $deviceService,
        GeoLocationService $geoService,
        AccountSharingDetectionService $accountSharingDetectionService
    ) {
        $this->userRepo = $userRepo;
        $this->userLogRepo = $userLogRepo;
        $this->sessionRepo = $sessionRepo;
        $this->deviceService = $deviceService;
        $this->geoService = $geoService;
        $this->accountSharingDetectionService = $accountSharingDetectionService;
    }

    public function login(array $credentials, ?string $deviceId, array $deviceData, ?string $ip)
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['error' => 'Invalid credentials']);
        }

        $user = Auth::user();

        if (!$user) {
            throw ValidationException::withMessages(['error' => 'Authentication failed']);
        }

        $fingerprint = $this->deviceService->generateDeviceFingerprint($deviceData);

        if (!$this->deviceService->isDeviceAllowed($user->id, $deviceId, $user->max_devices)) {
            throw ValidationException::withMessages(['error' => 'Too many devices.']);
        }

        $locationData = $this->geoService->getLocationFromIP($ip);
        $location = $locationData['city'] ?? 'Unknown';

        if ($this->accountSharingDetectionService->detectAccountSharing($user, $deviceId, $ip, $location)) {
            event(new SuspiciousLoginDetected($user->id, $ip, $deviceId, $location));
            throw ValidationException::withMessages(['error' => 'Suspicious activity detected.']);
        }

        $this->sessionRepo->createSession([
            'user_id' => $user->id,
            'device_id' => $deviceId,
            'fingerprint' => $fingerprint,
            'ip_address' => $ip,
            'location' => $location,
        ]);

        $this->userLogRepo->logLogin($user->id, $ip, $deviceId, $location);


        $token = $user->createToken('authToken')->plainTextToken;

        return $token;
    }

    public function verifyMfa(string $email, int $mfaCode, ?string $deviceId, array $deviceData, ?string $ip)
    {
        // No time, so I just made this like OTP
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            throw ValidationException::withMessages(['error' => 'User not found']);
        }

        $cacheKey = "user:{$user->id}:mfa";

        $storedMfaCode = Cache::store('redis')->get($cacheKey);

        if (!$storedMfaCode || $storedMfaCode != $mfaCode) {
            throw ValidationException::withMessages(['error' => 'Invalid MFA code']);
        }

        Cache::store('redis')->delete($cacheKey);

        $locationData = $this->geoService->getLocationFromIP($ip);
        $location = $locationData['city'] ?? 'Unknown';
        $fingerprint = $this->deviceService->generateDeviceFingerprint($deviceData);


        $this->sessionRepo->createSession([
            'user_id' => $user->id,
            'device_id' => $deviceId,
            'fingerprint' => $fingerprint,
            'ip_address' => $ip,
            'location' => $location,
        ]);

        $this->userLogRepo->logLogin($user->id, $ip, $deviceId, $location);

        $token = $user->createToken('authToken')->plainTextToken;

        return $token;
    }

    public function logout(string $deviceId)
    {
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();
        }

        $this->sessionRepo->deactivateSession($user->id, $deviceId);
    }
}
