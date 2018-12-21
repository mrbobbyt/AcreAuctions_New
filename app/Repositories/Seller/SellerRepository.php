<?php

namespace App\Repositories\Seller;

use App\Models\Seller;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use Illuminate\Database\Eloquent\Model;
use Exception;

class SellerRepository implements SellerRepositoryContract
{

    /**
     * Find seller by url
     *
     * @param string $slug
     * @return Model
     * @throws Exception
     */
    public function findBySlug(string $slug): Model
    {
        if ($seller = Seller::query()->where('slug', $slug)->first()) {
            return $seller;
        }

        throw new Exception('Sorry, slug is not created.');
    }

}
