<?php

namespace App\Services;

use App\Repositories\UserSessionRepository;
use Illuminate\Support\Facades\Http;

class GeoLocationService
{
    protected $userSessionRepo;

    public function __construct(UserSessionRepository $userSessionRepo)
    {
        $this->userSessionRepo = $userSessionRepo;
    }

    public function getLocationFromIP(string $ip)
    {
        $response = Http::get("https://ip-api.com/json/{$ip}");
        return $response->json();
    }

    public function isLocationSuspicious(int $userId, string $location)
    {
        // TODO: should use redis to limit database queries
        $sessions =  $this->userSessionRepo->findActiveSessions($userId);

        if(!count($sessions)) return false;

        $isLocationSuspicious = true;

        foreach ($sessions as $session) {
            if ($session->location === $location) {
                $isLocationSuspicious = false;
                break;
            }
        }

        return $isLocationSuspicious;
    }
}
