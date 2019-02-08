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
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findAll(array $data): LengthAwarePaginator
    {
        if ($data['body']) {
            if (count($data['body']) === 1 && array_key_exists('property_type', $data['body'])) {
                return $this->findOnlyPropertyType($data['body']['property_type']);
            }
            return $this->findByParams($data);
        }
        $listings = Listing::paginate(5);

        return $listings;
    }


    /**
     * Find all listings with requested fields
     * @param array $data
     * @return LengthAwarePaginator
     */
    protected function findByParams(array $data): LengthAwarePaginator
    {
        $geoParams = array_only($data['body'], ['acreage', 'state', 'city',
            'zip', 'longitude', 'latitude']);
        $priceParams = array_only($data['body'], ['price']);

        $county = [];
        if (isset($data['body']['county'])) {
            $county = explode(',', $data['body']['county']);
        }

        $propType = [];
        if (isset($data['body']['property_type'])) {
            $propType = explode(',', $data['body']['property_type']);
        }

        $saleType = [];
        if (isset($data['body']['sale_type'])) {
            $saleType = explode(',', $data['body']['sale_type']);
        }

        $listings = Listing::
            whereHas('geo', function ($q) use ($geoParams) {
                $q->whereFields($geoParams);
            })
            ->whereHas('geo', function ($q) use ($county) {
                if ($county) {
                    $q->whereIn('county', $county);
                }
            })
            ->whereHas('price', function ($q) use ($priceParams) {
                $q->whereFields($priceParams);
            })
            ->whereHas('price', function ($q) use ($saleType) {
                if ($saleType) {
                    $q->whereIn('sale_type', $saleType);
                }
            })
            ->orWhere(function ($q) use ($propType) {
                if($propType) {
                    $q->whereIn('property_type', $propType);
                }
            })
            ->paginate(5);

        return $listings;
    }


    /**
     * Find only by property type
     * @param string $propType
     * @return LengthAwarePaginator
     */
    protected function findOnlyPropertyType(string $propType): LengthAwarePaginator
    {
        $props = explode(',', $propType);
        $listings = Listing::whereIn('property_type', $props)->paginate(5);

        return $listings;
    }


    public function getFilters()
    {

    }
}
