<?php

namespace App\Services\Seller\Contracts;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

interface SellerServiceContract
{

    /**
     * @param array $data
     * @return Model
     * @throws Throwable
     */
    public function create(array $data);


    /**
     * Make seller verified
     *
     * @param object $seller
     * @return bool
     */
    public function verify(object $seller);


    /**
     * Check user`s permission to make action
     *
     * @param int $id
     * @return Model $seller
     * @throws Exception
     */
    public function checkPermission(int $id);


    /**
     * Update seller
     *
     * @param Model $seller
     * @param array $data
     * @return Model
     * @throws Exception
     */
    public function update(Model $seller, array $data);


    /**
     * Delete seller
     *
     * @param Model $seller
     * @throws Exception
     * @return bool
     */
    public function delete(Model $seller);
}
