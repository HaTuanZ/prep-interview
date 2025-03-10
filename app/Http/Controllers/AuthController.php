<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'      => 'required|email',
            'password'   => 'required',
        ]);

        $deviceId = $request->header("X-Device-ID");
        $deviceInfo = json_decode($request->header("X-Device-info") ?? '{}', true);

        try {
            $token = $this->authService->login(
                $request->only(['email', 'password']),
                $deviceId,
                $deviceInfo,
                $request->ip()
            );

            return response()->json(['message' => 'Login successful', 'token' => $token], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 403);
        }
    }

    public function verifyMfa(Request $request)
    {
        // No time, so I just made this like OTP and just need mfa code only
        $request->validate([
            'email'      => 'required|email',
            'mfaCode'   => 'required',
        ]);

        $deviceId = $request->header("X-Device-ID");
        $deviceInfo = json_decode($request->header("X-Device-info") ?? '{}', true);

        try {
            $token = $this->authService->verifyMfa(
                $request->input('email'),
                $request->input('mfaCode'),
                $deviceId,
                $deviceInfo,
                $request->ip()
            );

            return response()->json(['message' => 'Login successful', 'token' => $token], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 403);
        }
    }

    public function logout(Request $request)
    {
        $deviceId = $request->header("X-Device-ID");

        $this->authService->logout($deviceId);

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
