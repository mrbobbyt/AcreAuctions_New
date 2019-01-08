<?php
declare(strict_types = 1);

namespace App\Services\Seller\Contracts;

use App\Services\Seller\Exceptions\SellerAlreadyExistsException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

interface SellerServiceContract
{
    /**
     * @param array $data
     * @return Model
     * @throws Throwable
     * @throws JWTException
     * @throws SellerAlreadyExistsException
     */
    public function create(array $data);


    /**
     * Make seller verified
     * @param object $seller
     * @return bool
     */
    public function verify(object $seller);


    /**
     * Update seller
     * @param int $id
     * @param array $data
     * @return Model
     * @throws SellerAlreadyExistsException
     * @throws Throwable
     */
    public function update(array $data, int $id);


    /**
     * Delete seller
     * @param int $id
     * @throws Exception
     * @return bool
     */
    public function delete(int $id);

}
