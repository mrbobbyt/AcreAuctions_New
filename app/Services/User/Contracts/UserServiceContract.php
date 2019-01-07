<?php
declare(strict_types = 1);

namespace App\Services\User\Contracts;

use Exception;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Exceptions\JWTException;

interface UserServiceContract
{
    /**
     * Update user
     * @param array $data
     * @param int $id
     * @return false|JWTSubject
     * @throws JWTException
     */
    public function update(array $data, int $id);


    /**
     * Delete auth user
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id);

}
