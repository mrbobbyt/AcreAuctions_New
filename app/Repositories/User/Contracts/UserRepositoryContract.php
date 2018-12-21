<?php

namespace App\Repositories\User\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface UserRepositoryContract
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


    /**
     * Check if user exists in db
     *
     * @param string $email
     * @return bool
     */
    public function checkUserExists(string $email);

}
