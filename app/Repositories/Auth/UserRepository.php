<?php

namespace App\Repositories\Auth;

use App\Models\User;
use App\Repositories\Auth\Contracts\UserRepoContract;

class UserRepository implements UserRepoContract
{

    public function findByPk(int $id)
    {
        //
    }


    public function findByEmail(string $email): User
    {
        return User::where('email', $email)->first();
    }
}