<?php
declare(strict_types = 1);

namespace App\Services\User;

use App\Models\Image;
use File;
use Illuminate\Database\Eloquent\Model;

use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Auth\Contracts\UserAuthServiceContract;
use App\Services\Telephone\Contracts\TelServiceContract;
use App\Services\User\Contracts\UserServiceContract;

use Exception;
use Throwable;

class UserService implements UserServiceContract
{
    protected $userRepo;
    protected $userAuthService;
    protected $telService;

    public function __construct(
        UserRepositoryContract $userRepo,
        UserAuthServiceContract $userAuthService,
        TelServiceContract $telService
    ) {
        $this->userRepo = $userRepo;
        $this->userAuthService = $userAuthService;
        $this->telService = $telService;
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
            $this->updateAvatar($data['image'], $id);
        }

        if ($data['tel']) {
            foreach ($data['tel']['tel'] as $key => $tel) {
                $this->telService->update($key, (int)$tel, $id);
            }
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
        $this->deleteAvatar($user);
        $this->telService->delete($user);
        $user->delete();

        return true;
    }


    /**
     * Update User avatar
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    protected function updateAvatar(array $data, int $id): bool
    {
        $image = Image::query()
            ->where([
                ['entity_id', $id],
                ['entity_type', Image::TYPE_USER_AVATAR]
            ])
            ->first();

        if ($image === null) {
            return $this->userAuthService->createAvatar($data, $id);
        }

        if (File::exists(get_image_path($image->name))) {
            File::delete(get_image_path($image->name));
        }
        $image->name = upload_image($data['avatar'], 'avatar');
        $image->updated_at = date('Y-m-d H:i:s');

        return $image->saveOrFail();
    }


    /**
     * Delete User avatar
     * @param Model $user
     * @return bool
     */
    protected function deleteAvatar(Model $user): bool
    {
        if ($user->avatar) {
            if (File::exists(get_image_path($user->avatar->name))) {
                File::delete(get_image_path($user->avatar->name));
            }
            $user->avatar->delete();
        }

        return true;
    }
}
