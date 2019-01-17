<?php
declare(strict_types = 1);

namespace App\Services\User;

use App\Models\Image;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Auth\Contracts\UserAuthServiceContract;
use App\Services\User\Contracts\UserServiceContract;
use File;
use Illuminate\Database\Eloquent\Model;

use Exception;
use Throwable;

class UserService implements UserServiceContract
{
    protected $userRepo;
    protected $userAuthService;

    public function __construct(UserRepositoryContract $userRepo, UserAuthServiceContract $userAuthService)
    {
        $this->userRepo = $userRepo;
        $this->userAuthService = $userAuthService;
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

        if (File::exists(get_image_path('User', $image->name))) {
            File::delete(get_image_path('User', $image->name));
        }
        $image->name = upload_image($data['avatar'], 'User', 'avatar');
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
        if (File::exists(get_image_path('User', $user->avatar->name))) {
            File::delete(get_image_path('User', $user->avatar->name));
        }

        if ($user->avatar) {
            $user->avatar->delete();
        }

        return true;
    }
}
