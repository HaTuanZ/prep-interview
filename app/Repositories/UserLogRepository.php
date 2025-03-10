<?php

namespace App\Repositories;

use App\Models\UserLog;

class UserLogRepository
{
    public function logLogin(int $userId, string $ip, string $deviceId, string $location)
    {
        return UserLog::create([
            'user_id'   => $userId,
            "action" => "LOGIN", // TODO: Move it into a strong type class
            'metadata'  => [
                'ip'        => $ip,
                'device_id' => $deviceId,
                'location'  => $location
            ]
        ]);
    }

    public function getRecentLogs(int $userId, int $limit = 20)
    {
        return UserLog::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
