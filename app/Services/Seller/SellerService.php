<?php
declare(strict_types = 1);

namespace App\Services\Seller;

use App\Models\Email;
use App\Models\Image;
use App\Models\Seller;
use App\Models\Telephone;
use App\Services\User\Contracts\UserServiceContract;
use Exception;
use File;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\Seller\Contracts\SellerServiceContract;
use Illuminate\Http\UploadedFile;
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
        $data['body']['user_id'] = $this->userService->getID();

        // Create slug from title
        $data['body']['slug'] = $this->makeUrl($data['body']['title']);
        if ($this->sellerRepo->findBySlug($data['body']['slug'])) {
            throw new Exception('Seller with the same name already exists, please, choose another.', 400);
        }

        $seller = $this->model->query()->make()->fill($data['body']);

        if (!$seller->saveOrFail()) {
            throw new Exception('Can not save seller');
        }

        // Create images
        foreach ($data['image'] as $name => $item) {
            if ($item && !$this->createImages($name, $item, $seller->id)) {
                throw new Exception('Can not save '. $name);
            }
        }

        if ($data['email']) {
            $this->createEmail($data['email'], $seller->id);
        }

        if ($data['tel']) {
            $this->createTelephone($data['tel'], $seller->id);
        }

        return $seller;
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

        if ($seller && $seller->user_id !== $userID) {
            throw new Exception('You have no permission.');
        }

        if (empty($seller)) {
            throw new Exception('Seller not found.');
        }

        return $seller;
    }


    /**
     * Update seller
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

        // Update images
        foreach ($data['image'] as $name => $item) {
            if ($item && !$this->updateImages($name, $item, $seller->id)) {
                throw new Exception('Can not save '. $name);
            }
        }

        foreach ($data['body'] as $key=>$property) {
            $seller->$key = $property;
        }

        $seller->saveOrFail();

        return $seller;
    }


    /**
     * Delete seller
     * @param Model $seller
     * @throws Exception
     * @throws Throwable
     * @return bool
     */
    public function delete(Model $seller): bool
    {
        // Delete images
        if (!$this->deleteImages($seller)) {
            throw new Exception('Can not delete images.');
        }

        if (!$seller->delete()) {
            throw new Exception('Can not delete seller.');
        }

        return true;
    }


    /**
     * Upload image
     * @param string $name
     * @param UploadedFile $item
     * @param int $id
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    protected function createImages(string $name, UploadedFile $item, int $id): bool
    {
        $image = Image::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => ($name === 'logo') ? Image::TYPE_SELLER_LOGO : Image::TYPE_SELLER_COVER,
            'name' => upload_image($item, class_basename($this->model), $name),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return $image->saveOrFail();
    }


    /**
     * Update User avatar
     * @param string $name
     * @param UploadedFile $item
     * @param int $id
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    protected function updateImages(string $name, UploadedFile $item, int $id): bool
    {
        $image = Image::query()
            ->where([
                ['entity_id', $id],
                ['entity_type', ($name === 'logo') ? Image::TYPE_SELLER_LOGO : Image::TYPE_SELLER_COVER]
            ])->first();

        $image->name = upload_image($item, class_basename($this->model), $name);
        $image->updated_at = date('Y-m-d H:i:s');

        return $image->saveOrFail();
    }


    /**
     * Delete User avatar
     * @param Model $seller
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    protected function deleteImages(Model $seller): bool
    {
        if ($seller->logo) {
            if (File::exists(public_path('images/Seller/' . $seller->logo->name))) {
                File::delete(public_path('images/Seller/' . $seller->logo->name));
            }
            $seller->logo->delete();
        }

        if ($seller->cover) {
            if (File::exists(public_path('images/Seller/' . $seller->cover->name))) {
                File::delete(public_path('images/Seller/' . $seller->cover->name));
            }
            $seller->cover->delete();
        }

        return true;
    }


    /**
     * Save emails
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function createEmail(array $data, int $id)
    {
        foreach ($data as $email) {
            $model = Email::query()->make()->fill([
                'entity_id' => $id,
                'entity_type' => Email::TYPE_SELLER,
                'email' => $email,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $model->saveOrFail();
        }
    }


    /**
     * Save telephones
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function createTelephone(array $data, int $id)
    {
        foreach ($data as $tel) {
            $model = Telephone::query()->make()->fill([
                'entity_id' => $id,
                'entity_type' => Telephone::TYPE_SELLER,
                'number' => $tel,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $model->saveOrFail();
        }
    }
}
