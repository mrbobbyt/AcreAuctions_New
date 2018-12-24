<?php

namespace App\Services\Seller;

use App\Models\Seller;
use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\Seller\Contracts\SellerServiceContract;
use Throwable;

class SellerService implements SellerServiceContract
{

    protected $model;
    protected $sellerRepo;

    public function __construct(Seller $seller, SellerRepositoryContract $sellerRepo)
    {
        $this->model = $seller;
        $this->sellerRepo = $sellerRepo;
    }


    /**
     * Create new seller
     *
     * @param array $data
     * @return Model
     * @throws Throwable
     * @throws Exception
     */
    public function create(array $data): Model
    {
        // Create slug from title
        $data['slug'] = $this->makeUrl($data['title']);
        if ($this->sellerRepo->findBySlug($data['slug'])) {
            throw new Exception('Seller with the same name already exists, please, choose another.', 400);
        }

        // Upload logo
        if (isset($data['logo']) && $data['logo']) {
            $name = $this->uploadImage($data['logo'], 'logo');
            $data['logo'] = $name;
        }

        // Upload cover
        if (isset($data['cover']) && $data['cover']) {
            $name = $this->uploadImage($data['cover'], 'cover');
            $data['cover'] = $name;
        }

        $seller = $this->model->query()->make()->fill($data);

        if ($seller->saveOrFail()) {
            return $seller;
        }

        throw new Exception('Can not save seller');
    }


    /**
     * Return slug created from title
     *
     * @param string $title
     * @return string
     */
    public function makeUrl(string $title): string
    {
        return preg_replace('/[^a-z0-9]+/i', '_', $title);
    }


    /**
     * Make seller verified
     *
     * @param object $seller
     * @return bool
     */
    public function verify(object $seller): bool
    {
        $seller['is_verified'] = 1;

        return $seller->saveOrFail();
    }


    /**
     * Upload image into server
     *
     * @param string $img
     * @param string $type
     * @return string
     * @throws Exception
     */
    protected function uploadImage($img, $type)
    {
        $name = time() .'_'. $type .'_'. $img->getClientOriginalName();
        if (!$img->move('images/seller', $name)) {
            throw new Exception('Can not upload photo.', 500);
        }

        return $name;
    }
}
