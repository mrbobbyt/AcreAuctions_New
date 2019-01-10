<?php
declare(strict_types = 1);

namespace App\Repositories\SearchListing;

use App\Models\Listing;

class SearchListingRepository
{
    public function findAll()
    {
        $listings = Listing::with(['geo', 'images'])->get();

        return $listings;
    }


    public function findByParams(array $geoParams, array $price)
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
