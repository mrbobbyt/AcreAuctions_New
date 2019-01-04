<?php
declare(strict_types = 1);

namespace App\Repositories\Listing;

use App\Http\Resources\ListingResource;
use App\Models\Image;
use App\Models\Listing;
use App\Models\ListingGeo;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Services\User\Contracts\UserServiceContract;
use Exception;
use Illuminate\Database\Eloquent\Model;

class ListingRepository implements ListingRepositoryContract
{

    /**
     * Find listing by url
     * @param string $slug
     * @return Model | bool
     * @throws Exception
     */
    public function findBySlug(string $slug)
    {
        if ($listing = Listing::query()->where('slug', $slug)->first()) {
            return $listing;
        }

        throw new Exception('Listing not exist.', 404);
    }


    /**
     * Find listing by id
     * @param int $id
     * @return Model | bool
     */
    public function findByPk(int $id)
    {
        if ($listing = Listing::query()->find($id)) {
            return $listing;
        }

        return false;
    }


    /**
     * Get related images
     * @param ListingResource $listing
     * @return array
     */
    protected function getImages(ListingResource $listing): array
    {
        return $listing->images()
            ->where('entity_type', Image::TYPE_LISTING)
            ->get()->pluck('name')->toArray();
    }


    /**
     * Get related images
     * @param ListingResource $listing
     * @return array
     */
    public function getImageNames(ListingResource $listing): array
    {
        $array = [];
        foreach ($this->getImages($listing) as $i) {
            array_push($array, public_path().'/images/Listing/'.$i);
        }

        return $array;
    }


    /**
     * Get seller id
     * @return int
     * @throws JWTException
     */
    public function findSellerById(): int
    {
        $user = app(UserServiceContract::class)->authenticate();
        return $user->seller->id;
    }


    /**
     * Find geo listing by listing id
     * @param int $id
     * @return Model
     * @throws Exception
     */
    public function findGeoByPk(int $id): Model
    {
        if ($listing = ListingGeo::query()->where('listing_id', $id)->first()) {
            return $listing;
        }

        throw new Exception('Geo Listing not exist.', 404);
    }

}
