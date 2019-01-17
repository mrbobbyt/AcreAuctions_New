<?php
declare(strict_types = 1);

namespace App\Services\Auth\Contracts;

use Illuminate\Database\Eloquent\Model;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

interface UserAuthServiceContract
{
    /**
     * Create User
     * @param array $data
     * @return Model
     * @throws Throwable
     */
    public function create(array $data);


    /**
     * Create token for new User
     * @param $user
     * @throws JWTException
     * @return string
     */
    public function createToken($user);


    /**
     * Reset user password
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    public function resetPassword(array $data);


    /**
     * Send email with invitation token when user forgot password
     * @param array $data
     * @throws Throwable
     */
    public function sendEmailWithToken(array $data);


    /**
     * Create or login user via socials
     * @param array $data
     * @return array
     * @throws JWTException
     * @throws Throwable
     */
    public function createOrLogin(array $data);


    /**
     * Create User avatar
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function createAvatar(array $data, int $id);
}
