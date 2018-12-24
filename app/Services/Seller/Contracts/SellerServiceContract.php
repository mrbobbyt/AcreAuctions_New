<?php

namespace App\Services\Seller\Contracts;

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

}
