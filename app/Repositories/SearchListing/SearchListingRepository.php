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
        $listings = Listing::with(['images', 'geo', 'price'])->get();

        return $listings;
    }


    /**
     * Find all listings with requested fields
     * @param array $data
     * @return Collection
     */
    public function findByParams(array $data): Collection
    {
        $geoParams = array_only($data['body'], ['acreage', 'state', 'city', 'county', 'zip', 'longitude', 'latitude']);
        $priceParams = array_only($data['body'], ['price']);

        $listings = Listing::whereHas('geo', function ($q) use ($geoParams) {
            $q->whereFields($geoParams);
        })
        ->whereHas('price', function ($q) use ($priceParams) {
            $q->whereFields($priceParams);
        }
        )->with(['images', 'geo', 'price', 'sellerWithLogo'])->get();

        return $listings;
    }
}
