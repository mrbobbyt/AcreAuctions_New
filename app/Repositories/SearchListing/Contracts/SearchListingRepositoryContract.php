<?php
declare(strict_types = 1);

namespace App\Repositories\SearchListing\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
     * Find all listings at homepage
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findHomeListings(array $data);
}
