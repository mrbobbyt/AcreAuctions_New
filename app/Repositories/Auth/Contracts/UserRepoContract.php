<?php

namespace App\Repositories\Auth\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface UserRepoContract
{

    /**
     * Find user using id
     *
     * @param int $id
     * @return Model
     */
    public function findByPk(int $id);


    /**
     * Find user using email
     *
     * @param string $email
     * @return User
     */
    public function findByEmail(string $email);

}