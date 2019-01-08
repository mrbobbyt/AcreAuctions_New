<?php
declare(strict_types = 1);

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
     * @param object $seller
     * @return bool
     */
    public function verify(object $seller);


    /**
     * Update seller
     * @param int $id
     * @param array $data
     * @return Model
     * @throws Exception
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
