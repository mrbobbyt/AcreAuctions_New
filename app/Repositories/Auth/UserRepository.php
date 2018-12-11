<?php

namespace App\Repositories\Auth;

use App\Repositories\Auth\Contracts\UserRepoContract;

class UserRepository implements UserRepoContract
{

    public function findByPk(int $id)
    {
        //
    }
}