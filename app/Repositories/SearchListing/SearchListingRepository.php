<?php
declare(strict_types = 1);

namespace App\Repositories\SearchListing;

use App\Models\Listing;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

class SearchListingRepository implements SearchListingRepositoryContract
{
    /**
     * Find all listings
     * @return Collection
     */
    public function findAll(): Collection
    {
        $listings = Listing::with(['geo', 'images'])->get();

        return $listings;
    }


    /**
     * Find all listings with requested fields
     * @param array $geoParams
     * @param array $price
     * @return Collection
     */
    public function findByParams(array $geoParams, array $price): Collection
    {
        // if ($geo xor $price)
        $listing = Listing::whereHas('geo', function ($q) use ($geoParams) {
                $q->whereFields($geoParams);
            }
        )->with(['images', 'geo'])->get();
        // if ($geo && $price) { use two whereHas }

        return $listing;
    }
}
