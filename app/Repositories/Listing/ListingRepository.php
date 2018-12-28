<?php
declare(strict_types = 1);

namespace App\Repositories\Listing;

use App\Models\Listing;
use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use Illuminate\Database\Eloquent\Model;

class ListingRepository implements ListingRepositoryContract
{

    /**
     * Find listing by url
     * @param string $slug
     * @return Model | bool
     */
    public function findBySlug(string $slug)
    {
        if ($listing = Listing::query()->where('slug', $slug)->first()) {
            return $listing;
        }

        return false;
    }


    /**
     * Find listing by id
     * @param int $id
     * @return Model | bool
     */
    public function findByPk(int $id)
    {
        if ($listing = Listing::query()->find($id)) {
            return $listing;
        }

        return false;
    }

}
