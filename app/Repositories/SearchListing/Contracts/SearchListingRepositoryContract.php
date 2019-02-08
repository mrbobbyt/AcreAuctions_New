<?php
declare(strict_types = 1);

namespace App\Repositories\SearchListing\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SearchListingRepositoryContract
{

    /**
     * Find all listings
     * @param array $data
     * @return Collection
     */
    public function findAll(array $data);

    public function getFilters();
}
