<?php

namespace App\Services\User;


use App\Models\User;
use App\Repositories\User\Contracts\UserRepoContract;
use App\Services\User\Contracts\UserServiceContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use JWTAuth;

use Tymon\JWTAuth\Exceptions\JWTException;

class UserService implements UserServiceContract
{

    protected $model;
    protected $userRepo;


    public function __construct(User $user, UserRepoContract $userRepo)
    {
        $this->model = $user;
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
     * @return false|JWTSubject
     * @throws JWTException
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

}