<?php

namespace App\Services\User;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\User\Contracts\UserServiceContract;
use Exception;
use Tymon\JWTAuth\Contracts\JWTSubject;
use JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;

class UserService implements UserServiceContract
{

    protected $userRepo;


    public function __construct(UserRepositoryContract $userRepo)
    {
        $this->userRepo = $userRepo;
    }


    /**
     * Return authenticate user
     *
     * @throws JWTException
     * @return false|JWTSubject
     */
    public function authenticate()
    {
        return JWTAuth::parseToken()->authenticate();
    }


    /**
     * Update user
     *
     * @param array $data
     * @throws JWTException
     * @return false|JWTSubject
     */
    public function update(array $data)
    {
        $user = $this->authenticate();

        foreach ($data as $key=>$property) {
            $user->$key = $property;
        }
        $user->saveOrFail();

        return $user;
    }


    /**
     * Return id auth user
     *
     * @return int
     * @throw JWTException
     * @throws Exception
     */
    public function getID(): int
    {
        if ($user = JWTAuth::authenticate()) {
            return $user->id;
        }

        throw new Exception('Invalid token.');
    }


    /**
     * Delete auth user
     *
     * @param int $id
     * @throws Exception
     * @return bool
     */
    public function delete(int $id): bool
    {
        if ($id != $this->getID()) {
            throw new Exception('You are not permitted to delete this user.');
        }

        $user = $this->userRepo->findByPk($id);

        if ($user->delete()) {
            return true;
        }

        return false;
    }
}
