<?php
declare(strict_types = 1);

namespace App\Services\Seller;

use App\Models\Email;
use App\Models\Image;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Model;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Image\Contracts\AvatarServiceContract;
use App\Services\Telephone\Contracts\TelServiceContract;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\Seller\Contracts\SellerServiceContract;

use Throwable;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\Seller\Exceptions\SellerAlreadyExistsException;

class SellerService implements SellerServiceContract
{
    protected $model;
    protected $sellerRepo;
    protected $userRepo;
    protected $telService;
    protected $avatarService;

    public function __construct(
        Seller $seller,
        SellerRepositoryContract $sellerRepo,
        UserRepositoryContract $userRepo,
        TelServiceContract $telService,
        AvatarServiceContract $avatarService
    ) {
        $this->model = $seller;
        $this->sellerRepo = $sellerRepo;
        $this->userRepo = $userRepo;
        $this->telService = $telService;
        $this->avatarService = $avatarService;
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

        if ($data['image']) {
            $this->avatarService->create($data['image']['image'], $seller->id);
        }

        if ($data['email']) {
            foreach ($data['email']['email'] as $key => $item) {
                $this->createEmail($item, $seller->id);
            }
        }

        if ($data['telephones']) {
            foreach ($data['telephones']['telephones'] as $key => $item) {
                $this->telService->create((int)$item, $seller->id);
            }
        }

        return $seller;
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
            $this->avatarService->update($data['image']['image'], $id, Image::TYPE_SELLER_LOGO);
        }

        if ($data['email']) {
            foreach ($data['email']['email'] as $key => $item) {
                $this->updateEmail($key, $item, $id);
            }
        }
        if ($data['telephones']) {
            foreach ($data['telephones']['telephones'] as $key => $item) {
                $this->telService->update($key, $item, $id);
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
        $this->avatarService->delete($seller->avatar);
        $this->deleteEmails($seller);
        $this->telService->delete($seller);
        $seller->delete();

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
}
