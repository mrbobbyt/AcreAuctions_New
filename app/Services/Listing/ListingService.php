<?php
declare(strict_types = 1);

namespace App\Services\Listing;

use App\Models\Image;
use App\Models\Listing;
use App\Models\ListingGeo;
use App\Models\User;
use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Services\Listing\Contracts\ListingServiceContract;
use App\Services\User\Contracts\UserServiceContract;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class ListingService implements ListingServiceContract
{
    protected $model;
    protected $modelGeo;
    protected $listingRepo;

    public function __construct(Listing $listing, ListingGeo $listingGeo, ListingRepositoryContract $listingRepo)
    {
        $this->model = $listing;
        $this->modelGeo = $listingGeo;
        $this->listingRepo = $listingRepo;
    }


    /**
     * Create new seller
     * @param array $data
     * @return Model
     * @throws Throwable
     * @throws JWTException
     * @throws Exception
     */
    public function create(array $data): Model
    {
        $data['body']['seller_id'] = $this->listingRepo->findSellerById();

        // Create slug from title
        $data['body']['slug'] = makeUrl($data['body']['title']);
        if ($this->listingRepo->findBySlug($data['body']['slug'])) {
            throw new Exception('Listing with the same title already exists, please, choose another.', 400);
        }

        $listing = $this->model->query()->make()->fill($data['body']);

        if (!$listing->saveOrFail()) {
            throw new Exception('Can not save listing.');
        }

        $listingGeo = $this->modelGeo->query()->make()->fill($data['geo']);
        $listingGeo->listing_id = $listing->id;

        if (!$listingGeo->saveOrFail()) {
            throw new Exception('Can not save geo listing.');
        }

        if ($data['image'] && !$this->createImage($data['image'], $listing->id)) {
            throw new Exception('Can not save images.');
        }

        return $listing;

    }


    /**
     * @param array $item
     * @param string $id
     * @return bool
     * @throws Throwable
     */
    protected function createImage($item, $id): bool
    {
        $image = Image::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => Image::TYPE_LISTING,
            'name' => upload_image($item['image'], class_basename($this->model), 'listing'),
        ]);

        return $image->saveOrFail();
    }


    /**
     * Check user`s permission to make action
     * @param int $id
     * @return Model
     * @throws Exception
     * @throws JWTException
     */
    public function checkPermission(int $id): Model
    {
        $user = app(UserServiceContract::class)->authenticate();
        $listing = $this->listingRepo->findByPk($id);

        if ($listing && $listing->seller->id !== $user->id && $user->role !== User::ROLE_ADMIN) {
            throw new Exception('You have no permission.', 403);
        }

        if (empty($listing->seller->id)) {
            throw new Exception('Seller not found.', 404);
        }

        return $listing;
    }


    /**
     * Update listing
     * @param Model $listing
     * @param array $data
     * @return Model
     * @throws Exception
     * @throws Throwable
     */
    public function update(Model $listing, array $data): Model
    {
        if ($data['body']) {
            if (isset($data['body']['title']) && $data['body']['title']) {
                $data['body']['slug'] = makeUrl($data['body']['title']);
            }

            foreach ($data['body'] as $key => $property) {
                $listing->$key = $property;
            }

            $listing->saveOrFail();
        }

        if ($data['geo'] && !$this->updateGeo($data['geo'], $listing->id)) {
            throw new Exception('Can not update geo listing.', 500);
        }

        /***** create new image *****/
        if ($data['image'] && !$this->createImage($data['image'], $listing->id)) {
            throw new Exception('Can not update images.', 500);
        }
        /***** end *****/

        return $listing;
    }


    /**
     * Update geo listing
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function updateGeo(array $data, int $id): bool
    {
        $geo = $this->listingRepo->findGeoByPk($id);

        foreach ($data as $key => $property) {
            $geo->$key = $property;
        }

        return $geo->saveOrFail();
    }
}
