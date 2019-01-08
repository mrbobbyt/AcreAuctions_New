<?php
declare(strict_types = 1);

namespace App\Repositories\Listing;

use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Models\ListingGeo;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\Exceptions\NoPermissionException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class ListingRepository implements ListingRepositoryContract
{
    protected $userRepo;

    public function __construct(UserRepositoryContract $userRepo)
    {
        $this->userRepo = $userRepo;
    }


    /**
     * Find listing by url
     * @param string $slug
     * @return Model
     */
    public function findBySlug(string $slug): Model
    {
        return Listing::query()->where('slug', $slug)->firstOrFail();
    }


    /**
     * Find listing by id
     * @param int $id
     * @return Model
     */
    public function findByPk(int $id): Model
    {
        return Listing::query()->findOrFail($id);
    }


    /**
     * Check existing Listing by title
     * @param string $title
     * @return bool
     */
    public function findByTitle(string $title): bool
    {
        return Listing::query()->where('title', $title)->exists();
    }


    /**
     * Get related images
     * @param ListingResource $listing
     * @return array
     */
    protected function getImages(ListingResource $listing): array
    {
        return $listing->images()
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
            array_push($array, get_image_path('Listing', $i));
        }

        return $array;
    }


    /**
     * Get seller id
     * @return int
     * @throws JWTException
     * @throws TokenInvalidException
     * @throws TokenExpiredException
     */
    public function findSellerById(): int
    {
        $user = app(UserRepositoryContract::class)->authenticate();
        return $user->seller->id;
    }


    /**
     * Find geo listing by listing id
     * @param int $id
     * @return Model
     */
    public function findGeoByPk(int $id): Model
    {
        return ListingGeo::query()->where('listing_id', $id)->firstOrFail();
    }


    /**
     * Check user`s permission to make action
     * @param int $id
     * @return bool
     * @throws JWTException
     * @throws NoPermissionException
     */
    public function checkPermission(int $id): bool
    {
        return $this->userRepo->checkPermission( $this->findByPk($id)->seller->id );
    }

}
