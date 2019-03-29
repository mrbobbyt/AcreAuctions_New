<?php
declare(strict_types=1);

namespace App\Services\Auth\Contracts;

use Exception;
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
     * @param string $email
     * @param string $password
     * @return string
     */
    public function createToken(string $email, string $password = null): string;


    /**
     * Reset user password
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function resetPassword(string $email, string $password);


    /**
     * Send email with invitation token when user forgot password
     * @param string $reason
     * @param string $email
     * @param string $clientUrl
     */
    public function sendEmailWithToken(string $reason, string $email, string $clientUrl = '');


    /**
     * Create or login user via socials
     * @param array $data
     * @return array
     * @throws JWTException
     * @throws Throwable
     */
    public function createOrLogin(array $data);


    /**
     * Create token when user register
     * @param string $email
     * @param string $token
     * @return bool
     * @throws Throwable
     */
    public function createRegisterToken(string $email, string $token): bool;


    /**
     * Confirm user after registration
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function confirmUser(array $data);
}
