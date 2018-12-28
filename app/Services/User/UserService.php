<?php
declare(strict_types = 1);

namespace App\Services\User;

use App\Models\Image;
use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\User\Contracts\UserServiceContract;
use Exception;
use File;
use Throwable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use JWTAuth;
use Illuminate\Database\Eloquent\Model;

use Tymon\JWTAuth\Exceptions\JWTException;

class UserService implements UserServiceContract
{

    protected $userRepo;

    public function __construct(UserRepositoryContract $userRepo)
    {
        $this->userRepo = $userRepo;
    }


    /**
     * Return authenticate user
     * @throws JWTException
     * @throws Exception
     * @return JWTSubject
     */
    public function authenticate(): JWTSubject
    {
        return JWTAuth::parseToken()->authenticate();
    }


    /**
     * Update user
     * @param array $data
     * @return Model
     * @throws JWTException
     * @throws Exception
     * @throws Throwable
     */
    public function update(array $data): Model
    {
        $user = $this->userRepo->findByPk($this->getID());

        foreach ($data['body'] as $key=>$property) {
            $user->$key = $property;
        }
        $user->saveOrFail();
        if ($data['image']) {
            $this->updateAvatar($data['image'], $user->id);
        }

        return $user;
    }


    /**
     * Return id auth user
     * @throw JWTException
     * @throws Exception
     * @return int
     */
    public function getID(): int
    {
        if ($user = JWTAuth::parseToken()->authenticate()) {
            return $user->id;
        }

        throw new Exception('Invalid token.');
    }


    /**
     * Delete auth user
     * @param int $id
     * @throws Exception
     * @throws JWTException
     * @throws Throwable
     * @return bool
     */
    public function delete(int $id): bool
    {
        $user = $this->userRepo->findByPk($id);

        if (!$this->deleteAvatar($user)) {
            throw new Exception('Error image delete.');
        }

        if (!$user->delete()) {
            throw new Exception('Error user delete.');
        }

        return true;
    }


    /**
     * Update User avatar
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    protected function updateAvatar(array $data, int $id): bool
    {
        $image = Image::query()
            ->where([
                ['entity_id', $id],
                ['entity_type', Image::TYPE_USER_AVATAR]
            ])
            ->updateOrCreate([
                'name' => upload_image($data['avatar'], 'User', 'avatar'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return (bool)$image;
    }


    /**
     * Delete User avatar
     * @param Model $user
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    protected function deleteAvatar(Model $user): bool
    {
        if (File::exists(public_path('images/User/' . $user->avatar->name))) {
            File::delete(public_path('images/User/' . $user->avatar->name));
        }

        if ($user->avatar) {
            $user->avatar->delete();
        }

        return true;
    }


    /**
     * Check user`s permission to make action
     * @param int $id
     * @return bool
     * @throws Exception
     * @throws JWTException
     */
    public function checkPermission(int $id): bool
    {
        if ($id == $this->getID() || JWTAuth::user()->role === User::ROLE_ADMIN) {
            return true;
        }

        throw new Exception('You are not permitted to delete this user.');
    }
}
