<?php
declare(strict_types=1);

namespace App\Repositories\SearchListing;

use App\Models\Listing;
use App\Models\ListingGeo;
use App\Models\ListingStatus;
use App\Repositories\SearchListing\Contracts\SearchListingRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class SearchListingRepository implements SearchListingRepositoryContract
{
    const SORT_ORDER_ASC = 'asc';
    const SORT_ORDER_DESC = 'desc';

    /**
     * Find all listings with requested fields
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function findListings(array $data): LengthAwarePaginator
    {
        $listings = (new Listing)->newQuery();

        // Search by geo params by handwriting
        if (isset($data['address'])) {
            $address = $data['address'];
            $listings->whereHas('geo', function ($q) use ($address) {
                $q->where('county', 'like', '%' . $address . '%')
                    ->orWhere('city', 'like', '%' . $address . '%')
                    ->orWhere('zip', 'like', '%' . $address . '%');
            });
        }

        // Search by geo params
        $geoParams = \array_only($data, ['longitude', 'latitude']);
        if ($geoParams) {
            $listings->whereHas('geo', function ($q) use ($geoParams) {
                $q->whereFields($geoParams);
            });
        }

        // Multiple filter search by state in geo params
        if (isset($data['state'])) {
            $state = explode(',', $data['state']);
            $listings->whereHas('geo', function ($q) use ($state) {
                $q->whereIn('state', $state);
            });
        }

        // Search by range of acreage of listings
        if (isset($data['minSize'])) {
            if (isset($data['minSize']) && isset($data['maxSize'])) {
                $acreageParam = [$data['minSize'], $data['maxSize']];
                $listings->whereHas('geo', function ($q) use ($acreageParam) {
                    $q->whereBetween('acreage', $acreageParam);
                });
            } else {
                $acreageParam = $data['minSize'];
                $listings->whereHas('geo', function ($q) use ($acreageParam) {
                    $q->where('acreage', '>=', $acreageParam);
                });
            }
        }

        // Search by range of price of listings
        if (isset($data['minPrice'])) {
            if (isset($data['minPrice']) && isset($data['maxPrice'])) {
                $priceParam = [$data['minPrice'], $data['maxPrice']];
                $listings->whereHas('price', function ($q) use ($priceParam) {
                    $q->whereBetween('price', $priceParam);
                });
            } else {
                $priceParam = $data['minPrice'];
                $listings->whereHas('price', function ($q) use ($priceParam) {
                    $q->where('price', '>=', $priceParam);
                });
            }
        }

        // Multiple filter search by sale type (financing) in price params
        if (isset($data['sale_type'])) {
            $saleType = explode(',', $data['sale_type']);
            $listings->whereHas('price', function ($q) use ($saleType) {
                $q->whereIn('sale_type', $saleType);
            });
        }

        // Multiple filter search by property type in base listing params
        if (isset($data['property_type'])) {
            $propType = explode(',', $data['property_type']);
            $listings->where(function ($q) use ($propType) {
                $q->whereIn('property_type', $propType);
            });
        }

        // Get all available listings
        $listings->where('status', ListingStatus::TYPE_AVAILABLE);

        // Return sort listings
        if (isset($data['sort'])) {
            list($field, $dir) = explode(':', $data['sort']);
            $relation = $field === 'price' ? 'price' : 'geo';

            switch ($dir) {
                case self::SORT_ORDER_DESC:
                    return $listings
                        ->get()
                        ->sortByDesc($relation . '.' . $field)
                        ->paginate(5);

                case self::SORT_ORDER_ASC:
                default:
                    return $listings
                        ->get()
                        ->sortBy($relation . '.' . $field)
                        ->paginate(5);
            }
        }

        return $listings->paginate(5);
    }


    /**
     * Find all counties
     * @return array
     */
    public function getCounties()
    {
        return ListingGeo::query()
            ->groupBy('county')
            ->pluck('county')
            ->all();
    }


    /**
     * Find all states
     * @return array
     */
    public function getStates()
    {
        return ListingGeo::query()
            ->groupBy('state')
            ->pluck('state')
            ->all();
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
