<?php

namespace App\Repositories\Auth;

use App\Models\User;
use App\Repositories\Auth\Contracts\UserRepoContract;
use Illuminate\Database\Eloquent\Model;

class UserRepository implements UserRepoContract
{

    /**
     * Find user using id
     *
     * @param int $id
     * @return Model
     */
    public function findByPk(int $id): Model
    {
        return User::query()->findOrFail($id);
    }


    /**
     * Find user using email
     *
     * @param string $email
     * @return User
     */
    public function findByEmail(string $email): User
    {
        return User::query()->where('email', $email)->firstOrFail();
    }
}