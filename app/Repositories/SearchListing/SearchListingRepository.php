<?php
declare(strict_types = 1);

namespace App\Repositories\SearchListing;

use App\Models\Listing;
use App\Models\ListingGeo;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchListingRepository implements SearchListingRepositoryContract
{
    /**
     * Find all listings with requested fields
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findAll(array $data): LengthAwarePaginator
    {
        $listings = (new Listing)->newQuery();

        // Search by geo params
        $geoParams = array_only($data['body'], ['state', 'city', 'zip', 'longitude', 'latitude']);
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

        // Search by range of acreage of listings
        if (isset($data['body']['acreage'])) {
            $acreageParam = $data['body']['acreage'];
            $listings->whereHas('geo', function ($q) use ($acreageParam) {
                $q->where('acreage', '>=', $acreageParam);
            });
        }

        // Search by range of price of listings
        if (isset($data['body']['price'])) {
           $priceParam = $data['body']['price'];
           $listings->whereHas('price', function ($q) use ($priceParam) {
                if ($priceParam) {
                    $q->where('price', '>=', $priceParam);
                }
            });
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

        return $listings->paginate(5);
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
}
