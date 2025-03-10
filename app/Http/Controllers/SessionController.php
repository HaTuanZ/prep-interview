<?php

namespace App\Http\Controllers;

use App\Repositories\UserSessionRepository;
use App\Services\BehaviorAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    protected $sessionRepo;

    public function __construct(UserSessionRepository $sessionRepo)
    {
        $this->sessionRepo = $sessionRepo;
    }

    public function listSessions(Request $request)
    {
        $user = Auth::user();

        $sessions = $this->sessionRepo->findActiveSessions($user->id);

        return response()->json(['sessions' => $sessions], 200);
    }

    public function terminateSession(Request $request)
    {
        $request->validate(['session_id' => 'required|integer']);

        $this->sessionRepo->deleteSession($request->input('session_id'));

        return response()->json(['message' => 'Session terminated'], 200);
    }
}
