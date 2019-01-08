<?php
declare(strict_types = 1);

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\Exceptions\NoPermissionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use JWTAuth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class UserRepository implements UserRepositoryContract
{
    /**
     * Find user using id
     * @param int $id
     * @throws ModelNotFoundException
     * @return Model
     */
    public function findByPk(int $id): Model
    {
        return User::query()->findOrFail($id);
    }


    /**
     * Find user using email
     * @param string $email
     * @throws ModelNotFoundException
     * @return Model
     */
    public function findByEmail(string $email): Model
    {
        return User::query()->where('email', $email)->firstOrFail();
    }


    /**
     * Check if user exists in db
     * @param string $email
     * @throws ModelNotFoundException
     * @return bool
     */
    public function checkUserExists(string $email): bool
    {
        return User::query()->where('email', $email)->exists();
    }


    /**
     * Return authenticate user
     * @throws JWTException
     * @throws TokenExpiredException
     * @throws TokenInvalidException
     * @return JWTSubject
     */
    public function authenticate(): JWTSubject
    {
        return JWTAuth::parseToken()->authenticate();
    }


    /**
     * Return authenticate user
     * @throws JWTException
     * @return int
     */
    public function getId(): int
    {
        return $this->authenticate()->getJWTIdentifier();
    }


    /**
     * Check user`s permission to make action
     * @param int $id
     * @return bool
     * @throws JWTException
     * @throws NoPermissionException
     */
    public function checkPermission(int $id): bool
    {
        if ($id === $this->getId() || $this->isAdmin()) {
            return true;
        }

        throw new NoPermissionException();
    }


    /**
     * @return bool
     * @throws JWTException
     */
    public function isAdmin(): bool
    {
        return $this->authenticate()->role === User::ROLE_ADMIN;
    }


    /**
     * Check existing token
     * @return bool
     */
    public function checkToken(): bool
    {
        return (bool)JWTAuth::check(JWTAuth::getToken());
    }


    /**
     * Logout user and break token
     * @throws JWTException
     */
    public function breakToken()
    {
        return JWTAuth::invalidate(JWTAuth::getToken());
    }
}
