<?php
declare(strict_types = 1);

namespace App\Services\Listing\Contracts;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

interface ListingServiceContract
{

    /**
     * @param array $data
     * @return Model
     * @throws Throwable
     */
    public function create(array $data);


    /**
     * Check user`s permission to make action
     * @param int $id
     * @return Model
     * @throws Exception
     * @throws JWTException
     */
    public function checkPermission(int $id);


    /**
     * Update listing
     * @param Model $listing
     * @param array $data
     * @return Model
     * @throws Exception
     * @throws Throwable
     */
    public function update(Model $listing, array $data);

}
