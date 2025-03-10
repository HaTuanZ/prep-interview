<?php

use App\Http\Controllers\AccountSharingDetectionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\SessionController;
use App\Http\Middleware\CheckDeviceLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-mfa', [AuthController::class, 'verifyMfa']);


Route::middleware(['auth:sanctum', CheckDeviceLimit::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->withoutMiddleware(CheckDeviceLimit::class);

    Route::get('/sessions', [SessionController::class, 'listSessions']);
    Route::post('/sessions/terminate', [SessionController::class, 'terminateSession']);


    Route::get('lessons', [LessonController::class, 'index']);
});
