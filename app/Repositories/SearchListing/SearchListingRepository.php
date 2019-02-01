<?php
declare(strict_types = 1);

namespace App\Repositories\SearchListing;

use App\Models\Listing;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchListingRepository implements SearchListingRepositoryContract
{
    /**
     * Find all listings
     * @return LengthAwarePaginator
     */
    public function findAll(): LengthAwarePaginator
    {
        $listings = Listing::with(['images', 'geo', 'price', 'sellerWithLogo', 'docs',
            'subdivision', 'links', 'videos'])->paginate(5);

        return $listings;
    }


    /**
     * Find all listings with requested fields
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findByParams(array $data): LengthAwarePaginator
    {
        $listingParams = array_only($data['body'], ['property_type']);
        $geoParams = array_only($data['body'], ['acreage', 'state', 'city',
            'county', 'zip', 'longitude', 'latitude']);
        $priceParams = array_only($data['body'], ['price', 'sale_type']);

        $listings = Listing::whereHas('geo', function ($q) use ($geoParams) {
                $q->whereFields($geoParams);
            })
            ->whereHas('price', function ($q) use ($priceParams) {
                $q->whereFields($priceParams);
            })
            ->whereFields($listingParams)
            ->paginate(5);

        return $listings;
    }
}
