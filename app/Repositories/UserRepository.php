<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserLog;
use App\Models\UserSession;

class UserRepository
{
    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function getAllUsers() {
        // TODO: need truck feature

        return User::all();
    }
}
