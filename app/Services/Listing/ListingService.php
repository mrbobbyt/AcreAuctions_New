<?php
declare(strict_types = 1);

namespace App\Services\Listing;

use App\Models\Doc;
use App\Models\ListingPrice;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
use App\Models\Listing;
use App\Models\ListingGeo;

use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Services\Listing\Contracts\ListingServiceContract;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Services\Listing\Exceptions\ListingAlreadyExistsException;

class ListingService implements ListingServiceContract
{
    protected $model;
    protected $modelGeo;
    protected $modelPrice;
    protected $listingRepo;

    public function __construct(
        Listing $listing,
        ListingGeo $listingGeo,
        ListingPrice $listingPrice,
        ListingRepositoryContract $listingRepo
    ) {
        $this->model = $listing;
        $this->modelGeo = $listingGeo;
        $this->modelPrice = $listingPrice;
        $this->listingRepo = $listingRepo;
    }


    /**
     * Create new seller
     * @param array $data
     * @return Model
     * @throws JWTException
     * @throws ListingAlreadyExistsException
     * @throws Throwable
     * @throws TokenInvalidException
     * @throws TokenExpiredException
     */
    public function create(array $data): Model
    {
        $data['body']['seller_id'] = $this->listingRepo->findSellerById();

        if ($this->listingRepo->findByTitle($data['body']['title'])) {
            throw new ListingAlreadyExistsException();
        }
        $data['body']['slug'] = make_url($data['body']['title']);

        $listing = $this->model->query()->make()->fill($data['body']);
        $listing->saveOrFail();

        $listingGeo = $this->modelGeo->query()->make()->fill($data['geo']);
        $listingGeo->listing_id = $listing->id;
        $listingGeo->saveOrFail();

        $listingGeo = $this->modelPrice->query()->make()->fill($data['price']);
        $listingGeo->listing_id = $listing->id;
        $listingGeo->saveOrFail();

        if ($data['image']) {
            $this->createImage($data['image'], $listing->id);
        }

        if ($data['doc']) {
            $this->createDoc($data['doc'], $listing->id);
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
     * @param array $item
     * @param string $id
     * @return bool
     * @throws Throwable
     */
    protected function createDoc($item, $id): bool
    {

        /*TODO - create files in separate directory*/

        $image = Doc::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => Doc::TYPE_LISTING,
            'name' => upload_image($item['doc'], class_basename($this->model), 'doc'),
        ]);

        return $image->saveOrFail();
    }


    /**
     * Update listing
     * @param int $id
     * @param array $data
     * @return Model
     * @throws Throwable
     * @throws ListingAlreadyExistsException
     */
    public function update(array $data, int $id): Model
    {
        $listing = $this->listingRepo->findByPk($id);

        if ($data['body']) {
            if (isset($data['body']['title']) && $data['body']['title']) {
                if ($this->listingRepo->findByTitle($data['body']['title'])) {
                    throw new ListingAlreadyExistsException();
                }
                $data['body']['slug'] = make_url($data['body']['title']);
            }

            foreach ($data['body'] as $key => $property) {
                $listing->$key = $property;
            }
            $listing->saveOrFail();
        }

        if ($data['geo']) {
            $this->updateGeo($data['geo'], $id);
        }

        /***** create new image *****/
        if ($data['image']) {
            $this->createImage($data['image'], $id);
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


    /**
     * Delete listing and related models
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $listing = $this->listingRepo->findByPk($id);

        $geo = $this->listingRepo->findGeoByPk($listing->id);
        $geo->delete();

        $images = $listing->images;
        foreach ($images as $image) {
            $image->delete();
        }

        $listing->delete();

        return true;
    }

}
