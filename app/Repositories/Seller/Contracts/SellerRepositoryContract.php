<?php

namespace App\Repositories\Seller\Contracts;

use Illuminate\Database\Eloquent\Model;
use Exception;

interface SellerRepositoryContract
{

    /**
     * Find seller by url
     *
     * @param string $slug
     * @return Model
     * @throws Exception
     */
    public function findBySlug(string $slug);

}
