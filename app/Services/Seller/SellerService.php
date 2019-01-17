<?php
declare(strict_types = 1);

namespace App\Services\Seller;

use App\Models\Email;
use App\Models\Image;
use App\Models\Seller;
use App\Models\Telephone;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Seller\Exceptions\SellerAlreadyExistsException;
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
     * @throws JWTException
     * @throws SellerAlreadyExistsException
     * @throws Throwable
     */
    public function create(array $data): Model
    {
        $data['body']['user_id'] = $this->userRepo->getId();

        if ($this->sellerRepo->findByTitle($data['body']['title'])) {
            throw new SellerAlreadyExistsException();
        }
        $data['body']['slug'] = make_url($data['body']['title']);

        $seller = $this->model->query()->make()->fill($data['body']);

        $seller->saveOrFail();

        // Create images
        foreach ($data['image'] as $name => $item) {
            if ($item) {
                $this->createImages($name, $item, $seller->id);
            }
        }

        if ($data['email']) {
            foreach ($data['email']['email'] as $key => $item) {
                $this->createEmail($item, $seller->id);
            }
        }

        if ($data['tel']) {
            foreach ($data['tel']['tel'] as $key => $item) {
                $this->createTelephone((int)$item, $seller->id);
            }
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
     * @param array $data
     * @param int $id
     * @return Model
     * @throws SellerAlreadyExistsException
     * @throws Throwable
     */
    public function update(array $data, int $id): Model
    {
        $seller = $this->sellerRepo->findByPk($id);

        if ($data['image']) {
            foreach ($data['image'] as $name => $item) {
                if ($item) {
                    $this->updateImages($name, $item, $id);
                }
            }
        }

        if ($data['email']) {
            foreach ($data['email']['email'] as $key => $item) {
                $this->updateEmail($key, $item, $id);
            }
        }
        if ($data['tel']) {
            foreach ($data['tel']['tel'] as $key => $item) {
                $this->updateTelephone($key, $item, $id);
            }
        }

        if ($data['body']) {
            if (isset($data['body']['title']) && $data['body']['title']) {
                if ($this->sellerRepo->findByTitle($data['body']['title'])) {
                    throw new SellerAlreadyExistsException();
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
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        $seller = $this->sellerRepo->findByPk($id);
        $this->deleteImages($seller);
        $this->deleteEmails($seller);
        $this->deleteTelephones($seller);
        $seller->delete();

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

        if ($image === null) {
            return $this->createImages($name, $item, $id);
        }

        if (File::exists(get_image_path('Seller', $image->name))) {
            File::delete(get_image_path('Seller', $image->name));
        }
        $image->name = upload_image($item, class_basename($this->model), $name);
        $image->updated_at = date('Y-m-d H:i:s');

        return $image->saveOrFail();
    }


    /**
     * Delete User avatar
     * @param Model $seller
     * @return bool
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
     * @param string $email
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function createEmail(string $email, int $id)
    {
        $model = Email::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => Email::TYPE_SELLER,
            'email' => $email,
        ]);

        return $model->saveOrFail();
    }


    /**
     * Save telephones
     * @param int $tel
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function createTelephone(int $tel, int $id)
    {
        $model = Telephone::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => Telephone::TYPE_SELLER,
            'number' => $tel,
        ]);

        return $model->saveOrFail();
    }


    /**
     * @param int $key
     * @param string $item
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function updateEmail(int $key, string $item, int $id): bool
    {
        if ($email = $this->sellerRepo->findEmail($key, $id)) {
            $email->email = $item;
            return $email->saveOrFail();
        }

        return $this->createEmail($item, $id);
    }


    /**
     * @param int $key
     * @param string $item
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    protected function updateTelephone(int $key, string $item, int $id): bool
    {
        if ($tel = $this->sellerRepo->findTelephone($key, $id)) {
            $tel->number = $item;
            return $tel->saveOrFail();
        }
        return $this->createEmail($item, $id);
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
