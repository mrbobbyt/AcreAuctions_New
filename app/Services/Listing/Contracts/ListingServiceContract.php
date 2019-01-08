<?php
declare(strict_types = 1);

namespace App\Services\Listing\Contracts;

use App\Services\Listing\Exceptions\ListingAlreadyExistsException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

interface ListingServiceContract
{
    /**
     * @param array $data
     * @return Model
     * @throws JWTException
     * @throws ListingAlreadyExistsException
     * @throws Throwable
     * @throws TokenInvalidException
     * @throws TokenExpiredException
     */
    public function create(array $data);


    /**
     * Update listing
     * @param int $id
     * @param array $data
     * @return Model
     * @throws Throwable
     * @throws ListingAlreadyExistsException
     */
    public function update(array $data, int $id);


    /**
     * Delete listing and related models
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function delete(int $id);

}
