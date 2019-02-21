<?php
declare(strict_types = 1);

namespace App\Repositories\SearchListing\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SearchListingRepositoryContract
{

    /**
     * Find all listings
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findListings(array $data);


    /**
     * Find all counties
     * @return array
     */
    public function getCounties();


    /**
     * Find all states
     * @return array
     */
    public function getStates();


    /**
     * Get 8 random featured listings
     * @return Collection
     */
    public function findFeaturedListings();
}
