<?php
declare(strict_types = 1);

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryContract;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use JWTAuth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\JWTException;

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
     * @return JWTSubject
     */
    public function authenticate(): JWTSubject
    {
        return JWTAuth::user();
    }


    /**
     * Check user`s permission to make action
     * @param int $id
     * @return bool
     * @throws Exception
     * @throws JWTException
     */
    public function checkPermission(int $id): bool
    {
        if ($id === $this->authenticate()->getJWTIdentifier() || $this->authenticate()->role === User::ROLE_ADMIN) {
            return true;
        }

        throw new Exception('You have no permission.');
    }
}
