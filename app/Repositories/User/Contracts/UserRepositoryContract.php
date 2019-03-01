<?php
declare(strict_types = 1);

namespace App\Repositories\User\Contracts;

use App\Repositories\User\Exceptions\NoPermissionException;
use App\Repositories\User\Exceptions\NotEndedRegistrationException;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

interface UserRepositoryContract
{
    /**
     * Find user using id
     * @param int $id
     * @return Model
     */
    public function findByPk(int $id);


    /**
     * Find user using email
     * @param string $email
     * @return Model
     */
    public function findByEmail(string $email);


    /**
     * Check if user exists in db
     * @param string $email
     * @return bool
     */
    public function checkUserExists(string $email);


    /**
     * Return authenticate user
     * @throws JWTException
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     * @return JWTSubject
     */
    public function authenticate();


    /**
     * Return authenticate user
     * @throws JWTException
     * @return int
     */
    public function getId();


    /**
     * Logout user and break token
     * @throws JWTException
     */
    public function breakToken();


    /**
     * Check is user complete registration
     * @param Model $user
     * @throws NotEndedRegistrationException
     */
    public function checkCompleteRegister(Model $user);
}
