<?php
declare(strict_types = 1);

namespace App\Repositories\SearchListing\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SearchListingRepositoryContract
{

    /**
     * Find all listings
     * @return Collection
     */
    public function findAll();


    /**
     * Find all listings with requested fields
     * @param array $geoParams
     * @param array $price
     * @return Collection
     */
    public function findByParams(array $geoParams, array $price);

}
