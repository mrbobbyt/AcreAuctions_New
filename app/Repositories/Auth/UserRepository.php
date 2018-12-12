<?php

namespace App\Repositories\Auth;

use App\Models\User;
use App\Repositories\Auth\Contracts\UserRepoContract;

class UserRepository implements UserRepoContract
{

    /**
     * Find user using id
     *
     * @param int $id
     */
    public function findByPk(int $id)
    {
        //
    }


    /**
     * Find user using email
     *
     * @param string $email
     * @return User
     */
    public function findByEmail(string $email): User
    {
        return User::where('email', $email)->first();
    }
}