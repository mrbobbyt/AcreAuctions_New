<?php

namespace App\Services\Auth\Contracts;

use Illuminate\Database\Eloquent\Model;
use Throwable;

interface UserAuthServiceContract
{

    /**
     * Create User
     *
     * @param array $data
     * @return Model
     * @throws Throwable
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
     * @param $user
     * @return string
     */
    public function createToken($user);


    /**
     * Logout user and break token
     *
     */
    public function breakToken();


    /**
     * Reset user password
     *
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    public function resetPassword(array $data);


    /**
     * Send email with invitation token when user forgot password
     *
     * @param array $data
     * @throws Throwable
     */
    public function sendEmailWithToken(array $data);


    /**
     * Create reset token when user forgot password
     *
     * @param array $data
     * @throws Throwable
     * @return bool
     */
    public function createForgotToken(array $data);
}
