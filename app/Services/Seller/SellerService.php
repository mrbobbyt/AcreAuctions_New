<?php
declare(strict_types = 1);

namespace App\Services\Seller;

use App\Models\Seller;
use App\Services\User\Contracts\UserServiceContract;
use Exception;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\Seller\Contracts\SellerServiceContract;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class SellerService implements SellerServiceContract
{

    protected $model;
    protected $sellerRepo;
    protected $userService;

    public function __construct(Seller $seller, SellerRepositoryContract $sellerRepo, UserServiceContract $userService)
    {
        $this->model = $seller;
        $this->sellerRepo = $sellerRepo;
        $this->userService = $userService;
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
        $data['user_id'] = $this->userService->getID();

        // Create slug from title
        $data['slug'] = $this->makeUrl($data['title']);
        if ($this->sellerRepo->findBySlug($data['slug'])) {
            throw new Exception('Seller with the same name already exists, please, choose another.', 400);
        }

        // Upload logo
        if (isset($data['logo']) && $data['logo']) {
            $name = upload_image($data['logo'], class_basename($this->model), 'logo');
            $data['logo'] = $name;
        }

        // Upload cover
        if (isset($data['cover']) && $data['cover']) {
            $name = upload_image($data['cover'], class_basename($this->model), 'cover');
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
     * @param string $title
     * @return string
     */
    public function makeUrl(string $title): string
    {
        return preg_replace('/[^a-z0-9]+/i', '_', $title);
    }


    /**
     * Make seller verified
     * @param object $seller
     * @return bool
     */
    public function verify(object $seller): bool
    {
        $seller['is_verified'] = 1;

        return $seller->saveOrFail();
    }


    /**
     * Check user`s permission to make action
     * @param int $id
     * @return Model $seller
     * @throws Exception
     * @throws JWTException
     */
    public function checkPermission(int $id): Model
    {
        $userID = $this->userService->getID();
        $seller = $this->sellerRepo->findByPk($id);

        if ($seller->user_id == $userID) {
            return $seller;
        }

        throw new Exception('You are not permitted to update this seller.');
    }


    /**
     * Update seller
     *
     * @param Model $seller
     * @param array $data
     * @return Model
     * @throws Exception
     * @throws Throwable
     */
    public function update(Model $seller, array $data): Model
    {
        if (isset($data['body']['title']) && $data['body']['title']) {
            $data['body']['slug'] = $this->makeUrl($data['body']['title']);
        }

        // Upload logo
        if (isset($data['body']['logo']) && $data['body']['logo']) {
            $name = upload_image($data['body']['logo'], class_basename($this->model), 'logo');
            $data['body']['logo'] = $name;
        }

        // Upload cover
        if (isset($data['body']['cover']) && $data['body']['cover']) {
            $name = upload_image($data['body']['cover'], class_basename($this->model), 'cover');
            $data['body']['cover'] = $name;
        }

        foreach ($data['body'] as $key=>$property) {
            $seller->$key = $property;
        }
        $seller->saveOrFail();

        return $seller;
    }


    /**
     * Delete seller
     *
     * @param Model $seller
     * @throws Exception
     * @return bool
     */
    public function delete(Model $seller): bool
    {
        if ($seller->delete()) {
            return true;
        }

        return false;
    }
}
