<?php
declare(strict_types = 1);

namespace App\Services\Seller;

use App\Models\Email;
use App\Models\Image;
use App\Models\Seller;
use App\Models\Telephone;
use App\Repositories\User\Contracts\UserRepositoryContract;
use Exception;
use File;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\Seller\Contracts\SellerServiceContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class SellerService implements SellerServiceContract
{

    protected $model;
    protected $sellerRepo;
    protected $userRepo;

    public function __construct(Seller $seller, SellerRepositoryContract $sellerRepo, UserRepositoryContract $userRepo)
    {
        $this->model = $seller;
        $this->sellerRepo = $sellerRepo;
        $this->userRepo = $userRepo;
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
        $data['body']['user_id'] = $this->userRepo->getId();

        if ($this->sellerRepo->findByTitle($data['body']['title'])) {
            throw new Exception('Seller with the same title already exists, please, choose another.', 400);
        }
        $data['body']['slug'] = make_url($data['body']['title']);

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
     * Update seller
     * @param int $id
     * @param array $data
     * @return Model
     * @throws Exception
     * @throws Throwable
     */
    public function update(array $data, int $id): Model
    {
        $seller = $this->sellerRepo->findByPk($id);

        if ($data['image']) {
            foreach ($data['image'] as $name => $item) {
                if ($item && !$this->updateImages($name, $item, $id)) {
                    throw new Exception('Can not save ' . $name);
                }
            }
        }

        /**
         * only create new emails and telephones, not update
         * don`t understand how best realize without front
         */
        if ($data['email']) {
            $this->createEmail($data['email'], $id);
        }
        if ($data['tel']) {
            $this->createTelephone($data['tel'], $id);
        }
        /** end **/

        if ($data['body']) {
            if (isset($data['body']['title']) && $data['body']['title']) {
                if ($this->sellerRepo->findByTitle($data['body']['title'])) {
                    throw new Exception('Seller with the same title already exists, please, choose another.', 400);
                }
                $data['body']['slug'] = make_url($data['body']['title']);
            }

            foreach ($data['body'] as $key => $property) {
                $seller->$key = $property;
            }

            $seller->saveOrFail();
        }

        return $seller;
    }


    /**
     * Delete seller
     * @param int $id
     * @throws Exception
     * @throws Throwable
     * @return bool
     */
    public function delete(int $id): bool
    {
        $seller = $this->sellerRepo->findByPk($id);

        if (!$this->deleteImages($seller)) {
            throw new Exception('Can not delete images.');
        }

        if (!$this->deleteEmails($seller)) {
            throw new Exception('Can not delete emails.');
        }

        if (!$this->deleteTelephones($seller)) {
            throw new Exception('Can not delete telephones.');
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
            if (File::exists(get_image_path('Seller', $seller->logo->name))) {
                File::delete(get_image_path('Seller', $seller->logo->name));
            }
            $seller->logo->delete();
        }

        if ($seller->cover) {
            if (File::exists(get_image_path('Seller', $seller->cover->name))) {
                File::delete(get_image_path('Seller', $seller->cover->name));
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
            ]);

            return $model->saveOrFail();
        }
    }


    /**
     * Delete all related emails
     * @param Model $seller
     * @return mixed
     */
    protected function deleteEmails(Model $seller)
    {
       return $seller->emails->each(function ($item, $key) {
           $item->delete();
       });
    }


    /**
     * Delete all related telephones
     * @param Model $seller
     * @return mixed
     */
    protected function deleteTelephones(Model $seller)
    {
        return $seller->telephones->each(function ($item, $key) {
            $item->delete();
        });
    }
}
