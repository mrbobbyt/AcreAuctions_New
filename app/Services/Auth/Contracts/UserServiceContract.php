<?php

namespace App\Services\Auth\Contracts;

use App\Models\User;

interface UserServiceContract
{

    /**
     * Create User
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Throwable
     */
    public function create(array $data);


    /**
     * Create token for auth User
     *
     * @param array $data
     * @return string
     */
    public function getToken(array $data);


    /**
     * Create token for auth User
     *
     * @param array $data
     * @return string
     */
    public function getResetToken(array $data);


    /**
     * Create token for new User
     *
     * @param User $user
     * @return string
     */
    public function createToken(User $user);


    /**
     * Logout user and break token
     *
     */
    public function breakToken();


    /**
     * Return authenticate user
     *
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function authenticate();


    /**
     * Reset user password
     *
     * @param array $data
     * @return bool
     */
    public function resetPassword(array $data);

}