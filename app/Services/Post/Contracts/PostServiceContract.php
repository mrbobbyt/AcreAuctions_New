<?php
declare(strict_types = 1);

namespace App\Services\Post\Contracts;

use App\Services\Post\Exceptions\PostAlreadyExistsException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

interface PostServiceContract
{
    /**
     * @param array $data
     * @return Model
     * @throws JWTException
     * @throws PostAlreadyExistsException
     * @throws Throwable
     * @throws TokenInvalidException
     * @throws TokenExpiredException
     */
    public function create(array $data);


    /**
     * Delete Post and related models
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function delete(int $id);

}
