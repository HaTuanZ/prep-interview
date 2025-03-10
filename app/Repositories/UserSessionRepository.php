<?php

namespace App\Repositories;

use App\Models\UserSession;

class UserSessionRepository
{
    public function createSession(array $data)
    {
        return UserSession::create($data);
    }
    public function findActiveSessions(int $userId)
    {
        return UserSession::where('user_id', $userId)
            ->where('is_active', true)
            ->get();
    }

    public function getNumberOfUserDevices(int $userId) {
        return UserSession::where('user_id', $userId)
        ->selectRaw('COUNT(DISTINCT device_id) as device_count')
        ->first()
        ->device_count;
    }

    public function deactivateSession(int $userId, string $deviceId)
    {
        return UserSession::where('user_id', $userId)->where('deviceId', $deviceId)->update(["is_active" => false]);
    }

    public function deleteSession(int $sessionId)
    {
        return UserSession::where('id', $sessionId)->delete();
    }
}
