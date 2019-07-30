<?php
declare(strict_types=1);

namespace App\Services\Listing;

use App\Models\Doc;
use App\Models\FullsizePreview;
use App\Models\ListingPrice;
use App\Models\Subdivision;
use App\Models\Url;
use File;
use Illuminate\Database\Eloquent\Model;
use App\Models\Listing;
use App\Models\ListingGeo;

use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use App\Services\Listing\Contracts\ListingServiceContract;
use App\Services\Image\ImageService;

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
    protected $sub;
    protected $imageService;

    public function __construct(
        Listing $listing,
        ListingGeo $listingGeo,
        ListingPrice $listingPrice,
        ListingRepositoryContract $listingRepo,
        Subdivision $sub,
        ImageService $imageService
    )
    {
        $this->model = $listing;
        $this->modelGeo = $listingGeo;
        $this->modelPrice = $listingPrice;
        $this->listingRepo = $listingRepo;
        $this->sub = $sub;
        $this->imageService = $imageService;
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
        if ($this->listingRepo->findByTitle($data['body']['title'])) {
            throw new ListingAlreadyExistsException();
        }
        $data['body']['slug'] = make_url($data['body']['title']);

        $listing = $this->model->query()->make()->fill($data['body']);
        $listing->saveOrFail();

        $listingGeo = $this->modelGeo->query()->make()->fill($data['geo']);
        $listingGeo->listing_id = $listing->id;
        $listingGeo->saveOrFail();

        $listingPrice = $this->modelPrice->query()->make()->fill($data['price']);
        $listingPrice->listing_id = $listing->id;
        $listingPrice->saveOrFail();

        if ($data['utilities']) {
            $listing->utilities()->sync($data['utilities']['utilities']);
        }

        if ($data['subdivision']) {
            $sub = $this->sub->query()->make()->fill($data['subdivision']['subdivision']);
            $sub->listing_id = $listing->id;
            $sub->saveOrFail();
        }

        if ($data['image']) {
            foreach ($data['image']['image'] as $key => $item) {
                $this->imageService->create($item, $listing->id, 'listing');
            }
        }

        if ($data['doc']) {
            foreach ($data['doc'] as $arr) {
                foreach ($arr as $item) {
                    $this->createDoc($item, $listing->id);
                }
            }
        }

        if ($data['url']) {
            foreach ($data['url'] as $key => $arr) {
                $type = ($key === 'links' ? Url::TYPE_LISTING_LINK : Url::TYPE_LISTING_YOUTUBE);

                foreach ($arr as $item) {
                    $this->createUrl($type, $item, $listing->id);
                }
            }
        }

        return $listing;

    }


    /**
     * @param $item
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function createDoc($item, int $id): bool
    {
        $doc = Doc::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => Doc::TYPE_LISTING,
            'name' => upload_doc($item, $id, 'doc'),
        ]);

        return $doc->saveOrFail();
    }


    /**
     * @param int $type
     * @param array $item
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function createUrl(int $type, array $item, int $id): bool
    {
        $url = Url::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => $type,
            'name' => $item['name'],
            'desc' => $item['description'] ?? null
        ]);

        return $url->saveOrFail();
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
                if (
                    $this->listingRepo->findByTitle($data['body']['title'])
                    && $listing->title !== $data['body']['title']
                ) {
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

        if ($data['price']) {
            $this->updatePrice($data['price'], $id);
        }

        if ($data['utilities']) {
            $listing->utilities()->sync($data['utilities']['utilities']);
        }

        if ($data['subdivision']) {
            $this->updateSub($data['subdivision']['subdivision'], $id);
        }

        if ($data['image']) {
            $this->deleteRelatedImages($id);
            foreach ($data['image']['image'] as $image) {
                $this->imageService->create($image, $id, 'listing');
            }
        }

        if ($data['doc']) {
            $this->deleteRelatedDocs($id);
            foreach ($data['doc']['doc'] as $document) {
                $this->createDoc($document, $id);
            }
        }

        if ($data['url']) {
            $this->deleteRelatedLinks($id);
            $this->deleteRelatedVideos($id);
            foreach ($data['url'] as $key => $arr) {
                foreach ($arr as $urlKey => $item) {
                    $type = ($key === 'links' ? Url::TYPE_LISTING_LINK : Url::TYPE_LISTING_YOUTUBE);
                    $this->updateUrl($type, $urlKey, $item, $listing->id);
                }
            }
        }

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
     * Update price listing
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function updatePrice(array $data, int $id): bool
    {
        $price = $this->listingRepo->findPriceByPk($id);

        foreach ($data as $key => $property) {
            $price->$key = $property;
        }

        return $price->saveOrFail();
    }


    /**
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function updateSub(array $data, int $id): bool
    {
        $sub = $this->listingRepo->findSubByPk($id);

        foreach ($data as $key => $property) {
            $sub->$key = $property;
        }

        return $sub->saveOrFail();
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

        $geo = $this->listingRepo->findGeoByPk($id);
        $geo->delete();

        $price = $this->listingRepo->findPriceByPk($id);
        $price->delete();

        if ($sub = $this->listingRepo->findSubByPk($id)) {
            $sub->delete();
        }

        $images = $listing->images;
        if ($images !== null) {
            foreach ($images as $image) {
                $this->imageService->delete($image);
            }
        }

        $docs = $listing->docs;
        if ($docs !== null) {
            foreach ($docs as $doc) {
                $this->deleteDoc($doc);
            }
        }

        $links = $listing->links;
        if ($links !== null) {
            foreach ($links as $link) {
                $link->delete();
            }
        }
        $videos = $listing->videos;
        if ($videos !== null) {
            foreach ($videos as $video) {
                $video->delete();
            }
        }

        $listing->delete();

        return true;
    }


    /**
     * @param Model $doc
     * @return bool
     * @throws Exception
     */
    protected function deleteDoc(Model $doc): bool
    {
        if (File::exists(get_doc_path($doc->listing_id, $doc->name))) {
            File::delete(get_doc_path($doc->listing_id, $doc->name));
        }

        if ($doc) {
            $doc->delete();
        }

        return true;
    }


    /**
     * @param int $key
     * @param array $item
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function updateDoc(int $key, array $item, int $id): bool
    {
        if ($doc = $this->listingRepo->findDoc($key, $id)) {
            $this->deleteDoc($doc);
        }
        $this->createDoc($item, $id);

        return true;
    }


    /**
     * @param int $type
     * @param int $key
     * @param array $item
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function updateUrl(int $type, int $key, array $item, int $id): bool
    {
        if ($url = $this->listingRepo->findUrl($type, $key, $id)) {
            foreach ($item as $key => $property) {
                $url->$key = $property;
            }

            return $url->saveOrFail();
        }
        return $this->createUrl($type, $item, $id);
    }

    /**
     * @param int $id
     * @throws Exception
     */
    protected function deleteRelatedImages(int $id): void
    {
        $listingImages = $this->listingRepo->findByPk($id)->images;

        foreach ($listingImages as $image) {
            $relation = FullsizePreview::query()->where('fullsize_id', $image->id)->first();

            if ($relation) {
                $imagePreview = $this->listingRepo->findImage($relation->preview_id, $id);
                $this->imageService->delete($imagePreview);
            }

            $this->imageService->delete($image);
        }
    }

    /**
     * @param int $id
     * @throws Exception
     */
    protected function deleteRelatedDocs(int $id): void
    {
        $listingDocs = $this->listingRepo->findByPk($id)->docs;

        foreach ($listingDocs as $document) {
            $this->deleteDoc($document);
        }
    }

    /**
     * @param int $id
     */
    protected function deleteRelatedLinks(int $id): void
    {
        $listingLinks = $this->listingRepo->findByPk($id)->links;

        foreach ($listingLinks as $link) {
                $link->delete();
        }
    }

    /**
     * @param int $id
     */
    protected function deleteRelatedVideos(int $id): void
    {
        $listingVideos = $this->listingRepo->findByPk($id)->videos;

        foreach ($listingVideos as $video) {
            $video->delete();
        }
    }
}
