<?php

namespace App\Services;

use App\Repositories\UserLogRepository;
use App\Repositories\UserRepository;
use App\Repositories\UserSessionRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BehaviorAnalysisService
{
    protected $logRepo;
    protected $userRepo;
    protected $userSessionRepo;

    public function __construct(UserLogRepository $logRepo, UserRepository $userRepo, UserSessionRepository $userSessionRepo)
    {
        $this->logRepo = $logRepo;
        $this->userRepo = $userRepo;
        $this->userSessionRepo = $userSessionRepo;
    }

    public function analyzeLoginPatterns() {
        $users = $this->userRepo->getAllUsers();

        // TODO: loop through users to make analysis

        foreach ($users as $user) {

        }
    }

    public function isSuspiciousBehavior(int $userId, string $ipAddress, string $deviceId, string $location)
    {
        $logs = $this->logRepo->getRecentLogs($userId);

        foreach ($logs as $log) {
            if ($log->metadata['ip'] !== $ipAddress ||
                $log->metadata['device_id'] !== $deviceId ||
                $log->metadata['location'] !== $location) {

                $lastLoginTime = Carbon::parse($log->created_at);

                $currentNumberDevices = $this->userSessionRepo->getNumberOfUserDevices($userId);

                if ($currentNumberDevices > 3 && $lastLoginTime->diffInSeconds(now()) < 30) {
                    return true;
                }
            }
        }
        return false;
    }
}
