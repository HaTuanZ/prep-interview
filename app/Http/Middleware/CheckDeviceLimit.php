<?php

namespace App\Http\Middleware;

use App\Repositories\UserSessionRepository;
use App\Services\DeviceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckDeviceLimit
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }

    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $deviceId = $request->header('X-Device-ID');

        if (!$user || !$deviceId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $isDeviceAllowed = $this->deviceService->isDeviceAllowed($user->id, $deviceId, $user->max_devices);

        if (!$isDeviceAllowed) {
            return response()->json(['error' => 'Device limit exceeded'], 403);
        }

        return $next($request);
    }
}
