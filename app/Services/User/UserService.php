<?php
declare(strict_types = 1);

namespace App\Services\User;

use App\Models\Address;
use App\Models\Image;
use Illuminate\Database\Eloquent\Model;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Telephone\Contracts\TelServiceContract;
use App\Services\User\Contracts\UserServiceContract;
use App\Services\Address\Contracts\AddressServiceContract;
use App\Services\Image\Contracts\AvatarServiceContract;

use Exception;
use Throwable;

class UserService implements UserServiceContract
{
    protected $userRepo;
    protected $telService;
    protected $addressService;
    protected $avatarService;

    public function __construct(
        UserRepositoryContract $userRepo,
        TelServiceContract $telService,
        AddressServiceContract $addressService,
        AvatarServiceContract $avatarService
    ) {
        $this->userRepo = $userRepo;
        $this->telService = $telService;
        $this->addressService = $addressService;
        $this->avatarService = $avatarService;
    }


    /**
     * Update user
     * @param array $data
     * @param int $id
     * @return Model
     * @throws Exception
     * @throws Throwable
     */
    public function update(array $data, int $id): Model
    {
        $user = $this->userRepo->findByPk($id);

        foreach ($data['body'] as $key=>$property) {
            $user->$key = $property;
        }
        $user->saveOrFail();

        if ($data['image']) {
            $this->avatarService->update($data['image']['image'], $id, Image::TYPE_USER_AVATAR);
        }

        if ($data['telephones']) {
            foreach ($data['telephones']['telephones'] as $key => $tel) {
                $this->telService->update($key, (int)$tel, $id);
            }
        }

        if ($data['address']) {
            $this->addressService->update(Address::TYPE_USER, $data['address']['address'], $id);
        }

        return $user;
    }


    /**
     * Delete auth user
     * @param int $id
     * @throws Exception
     * @throws Throwable
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->userRepo->findByPk($id);

        if ($user->avatar) {
            $this->avatarService->delete($user->avatar);
        }
        if ($user->telephones) {
            $this->telService->delete($user);
        }
        if ($user->address) {
            $this->addressService->delete($user);
        }

        $user->delete();

        return true;
    }
}
