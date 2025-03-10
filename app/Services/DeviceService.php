<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DeviceService
{
    public function generateDeviceFingerprint(array $deviceData)
    {
        return hash('sha256', json_encode($deviceData));
    }

    public function isDeviceAllowed(int $userId, string $deviceId, int $maxDevices)
    {
        $cacheKey = "user:{$userId}:devices"; // TODO: Move this into config or constants class
        $devices = Cache::store('redis')->get($cacheKey, []);

        if (count($devices) >= $maxDevices && !in_array($deviceId, $devices)) {
            return false;
        }

        if(!in_array($deviceId, $devices)) {
            Cache::store('redis')->put($cacheKey, array_unique(array_merge($devices, [$deviceId])));
        }

        return true;
    }
}
