<?php

namespace App\Services;

use App\Events\SuspiciousLoginDetected;
use App\Models\User;

class AccountSharingDetectionService
{
    protected DeviceService $deviceService;
    protected GeoLocationService $geoLocationService;
    protected BehaviorAnalysisService $behaviorAnalysisService;

    public function __construct(
        DeviceService $deviceService,
        GeoLocationService $geoLocationService,
        BehaviorAnalysisService $behaviorAnalysisService
    ) {
        $this->deviceService = $deviceService;
        $this->geoLocationService = $geoLocationService;
        $this->behaviorAnalysisService = $behaviorAnalysisService;
    }

    public function detectAccountSharing(User $user, string $deviceId, string $ipAddress, string $location): bool
    {
        if ($this->geoLocationService->isLocationSuspicious($user->id, $location)) {
            return true;
        }

        if ($this->behaviorAnalysisService->isSuspiciousBehavior($user->id, $ipAddress, $deviceId, $location)) {
            return true;
        }

        return false;
    }
}
