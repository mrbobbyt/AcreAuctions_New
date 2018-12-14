<?php

namespace App\Services\User\Contracts;


use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\JWTException;

interface UserServiceContract
{

    /**
     * Return authenticate user
     *
     * @throws \Tymon\JWTAuth\Exceptions\JWTException
     */
    public function authenticate();


    /**
     * Update user
     *
     * @param array $data
     * @return false|JWTSubject
     * @throws JWTException
     */
    public function update(array $data);

}
