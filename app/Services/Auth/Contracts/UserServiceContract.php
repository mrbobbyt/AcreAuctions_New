<?php

namespace App\Services\Auth\Contracts;

use App\Models\User;
use Throwable;

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


    /**
     * Send email with invitation token when user forgot password
     *
     * @param array $data
     */
    public function sendEmailWithToken(array $data);


    /**
     * Create reset token when user forgot password
     *
     * @param array $data
     * @throws Throwable
     */
    public function createForgotToken(array $data);
}
