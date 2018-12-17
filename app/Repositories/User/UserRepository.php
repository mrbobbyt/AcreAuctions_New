<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\Contracts\UserRepoContract;
use Exception;
use Illuminate\Database\Eloquent\Model;

class UserRepository implements UserRepoContract
{

    /**
     * Find user using id
     *
     * @param int $id
     * @throws Exception
     * @return Model
     */
    public function findByPk(int $id): Model
    {
        if ($user = User::query()->find($id)) {
            return $user;
        }

        throw new Exception('User not exist.');
    }


    /**
     * Find user using email
     *
     * @param string $email
     * @throws Exception
     * @return User
     */
    public function findByEmail(string $email): User
    {
        if ($user = User::query()->where('email', $email)->first()) {
            return $user;
        }

        throw new Exception('User not exist.');
    }
}