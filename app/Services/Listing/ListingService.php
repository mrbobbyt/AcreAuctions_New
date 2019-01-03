<?php
declare(strict_types = 1);

namespace App\Services\Listing;

use App\Models\Image;
use App\Models\Listing;
use App\Models\ListingGeo;
use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Services\Listing\Contracts\ListingServiceContract;
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

}
