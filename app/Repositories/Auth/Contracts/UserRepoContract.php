<?php

namespace App\Repositories\Auth\Contracts;

use App\Models\User;

interface UserRepoContract
{

    /**
     * Find user using id
     *
     * @param int $id
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