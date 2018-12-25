<?php
declare(strict_types = 1);

namespace App\Repositories\Seller;

use App\Models\Seller;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use Illuminate\Database\Eloquent\Model;

class SellerRepository implements SellerRepositoryContract
{

    /**
     * Find seller by url
     *
     * @param string $slug
     * @return Model | bool
     */
    public function findBySlug(string $slug)
    {
        if ($seller = Seller::query()->where('slug', $slug)->first()) {
            return $seller;
        }

        return false;
    }


    /**
     * Find seller by id
     *
     * @param int $id
     * @return Model | bool
     */
    public function findByPk(int $id)
    {
        if ($seller = Seller::query()->find($id)) {
            return $seller;
        }

        return false;
    }

}
