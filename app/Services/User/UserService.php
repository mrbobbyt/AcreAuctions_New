<?php
declare(strict_types = 1);

namespace App\Services\User;

use App\Models\Image;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\User\Contracts\UserServiceContract;
use Exception;
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
     * @return false|JWTSubject
     */
    public function authenticate()
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

        foreach ($data as $key=>$property) {
            $user->$key = $property;
        }
        $user->saveOrFail();

        return $user;
    }


    /**
     * Return id auth user
     * @throw JWTException
     * @throws Exception
     */
    public function getID()
    {
        if ($user = JWTAuth::authenticate()) {
            return $user->id;
        }

        throw new Exception('Invalid token.');
    }


    /**
     * Delete auth user
     * @param int $id
     * @throws Exception
     * @throws JWTException
     * @return bool
     */
    public function delete(int $id): bool
    {
        if ($id != $this->getID()) {
            throw new Exception('You are not permitted to delete this user.');
        }

        $user = $this->userRepo->findByPk($id);

        if ($user->delete()) {
            return true;
        }

        return false;
    }


    /**
     * Update User avatar
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    public function updateAvatar(array $data, int $id): bool
    {
        $image = Image::query()
            ->where([
                ['entity_id', $id],
                ['entity_type', Image::TYPE_USER_AVATAR]
            ])
            ->update([
                'name' => upload_image($data['avatar'], 'User', 'avatar'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return (bool)$image;
    }
}
