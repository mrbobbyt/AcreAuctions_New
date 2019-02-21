<?php
declare(strict_types = 1);

namespace App\Repositories\SearchListing;

use App\Models\Listing;
use App\Models\ListingGeo;
use App\Models\ListingStatus;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SearchListingRepository implements SearchListingRepositoryContract
{
    /**
     * Find all listings with requested fields
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findListings(array $data): LengthAwarePaginator
    {
        $listings = (new Listing)->newQuery();

        // Search by geo params by handwriting
        if (isset($data['body']['address'])) {
            $address = $data['body']['address'];
            $listings->whereHas('geo', function ($q) use ($address) {
                $q->where('county', 'like', '%' . $address . '%')
                    ->orWhere('city', 'like', '%' . $address . '%')
                    ->orWhere('zip', 'like', '%' . $address . '%');
            });
        }

        // Search by geo params
        $geoParams = array_only($data['body'], ['longitude', 'latitude']);
        if ($geoParams) {
            $listings->whereHas('geo', function ($q) use ($geoParams) {
                $q->whereFields($geoParams);
            });
        }

        // Multiple filter search by county in geo params
        if (isset($data['body']['county'])) {
            $county = explode(',', $data['body']['county']);
            $listings->whereHas('geo', function ($q) use ($county) {
                $q->whereIn('county', $county);
            });
        }

        // Multiple filter search by state in geo params
        if (isset($data['body']['state'])) {
            $state = explode(',', $data['body']['state']);
            $listings->whereHas('geo', function ($q) use ($state) {
                $q->whereIn('state', $state);
            });
        }

        // Search by range of acreage of listings
        if (isset($data['body']['minSize'])) {
            if ( isset($data['body']['minSize']) && isset($data['body']['maxSize']) ) {
                $acreageParam = [$data['body']['minSize'], $data['body']['maxSize']];
                $listings->whereHas('geo', function ($q) use ($acreageParam) {
                    $q->whereBetween('acreage', $acreageParam);
                });
            } else {
                $acreageParam = $data['body']['minSize'];
                $listings->whereHas('geo', function ($q) use ($acreageParam) {
                    $q->where('acreage', '>=', $acreageParam);
                });
            }
        }

        // Search by range of price of listings
        if (isset($data['body']['minPrice'])) {
           if ( isset($data['body']['minPrice']) && isset($data['body']['maxPrice']) ) {
               $priceParam = [$data['body']['minPrice'], $data['body']['maxPrice']];
               $listings->whereHas('price', function ($q) use ($priceParam) {
                   $q->whereBetween('price', $priceParam);
               });
           } else {
               $priceParam = $data['body']['minPrice'];
               $listings->whereHas('price', function ($q) use ($priceParam) {
                   $q->where('price', '>=', $priceParam);
               });
           }
        }

        // Multiple filter search by sale type (financing) in price params
        if (isset($data['body']['sale_type'])) {
            $saleType = explode(',', $data['body']['sale_type']);
            $listings->whereHas('price', function ($q) use ($saleType) {
                $q->whereIn('sale_type', $saleType);
            });
        }

        // Multiple filter search by property type in base listing params
        if (isset($data['body']['property_type'])) {
            $propType = explode(',', $data['body']['property_type']);
            $listings->where(function ($q) use ($propType) {
                $q->whereIn('property_type', $propType);
            });
        }

        return $listings->where('status', ListingStatus::TYPE_AVAILABLE)->paginate(5);
    }


    /**
     * Find all counties
     * @return array
     */
    public function getCounties()
    {
        $counties = ListingGeo::query()
            ->groupBy('county')
            ->pluck('county')
            ->all();

        return $counties;
    }


    /**
     * Find all states
     * @return array
     */
    public function getStates()
    {
        $states = ListingGeo::query()
            ->groupBy('state')
            ->pluck('state')
            ->all();

        return $states;
    }


    /**
     * Get 8 random featured listings
     * @return Collection
     */
    public function findFeaturedListings(): Collection
    {
        return Listing::query()
            ->where('is_featured', 1)
            ->inRandomOrder()
            ->limit(8)
            ->get();
    }
}
