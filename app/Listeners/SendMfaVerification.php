<?php

namespace App\Listeners;

use App\Events\SuspiciousLoginDetected;
use App\Models\User;
use App\Notifications\SuspiciousLoginNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class SendMfaVerification
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SuspiciousLoginDetected $event): void
    {
        $cacheKey = "user:{$event->userId}:mfa";

        $mfa = random_int(10000, 999999);

        Cache::store('redis')->put($cacheKey, $mfa, 300);

        $user = User::find($event->userId);

        if ($user) {
            $user->notify(new SuspiciousLoginNotification($event->ip, $event->deviceId, $event->location, $mfa));
        }
    }
}
