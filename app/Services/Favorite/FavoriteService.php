<?php
declare(strict_types = 1);

namespace App\Services\Favorite;

use App\Models\Favorite;
use App\Repositories\Favorite\Contracts\FavoriteRepositoryContract;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Services\Favorite\Contracts\FavoriteServiceContract;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\Eloquent\Model;
use Exception;

class FavoriteService implements FavoriteServiceContract
{
    protected $userRepo;
    protected $favorRepo;

    public function __construct(UserRepositoryContract $userRepo, FavoriteRepositoryContract $favorRepo)
    {
        $this->userRepo = $userRepo;
        $this->favorRepo = $favorRepo;
    }


    /**
     * Create or delete listing
     * @param array $data
     * @return string
     * @throws Throwable
     * @throws JWTException
     * @throws Exception
     */
    public function action(array $data): string
    {
        $data['body']['user_id'] = $this->userRepo->getId();

        if ($favor =  $this->favorRepo->findByPk($data)) {
            $this->delete($favor);
            $result = 'Favorite listing successfully deleted.';
        } else {
            $this->create($data['body']);
            $result = 'Favorite listing successfully created.';
        }

        return $result;
    }


    /**
     * @param array $data
     * @return bool
     * @throws Throwable
     * @throws JWTException
     */
    protected function create(array $data): bool
    {
        $favor = Favorite::query()->make()->fill($data);
        $favor->user_id = $data['user_id'];

        return $favor->saveOrFail();
    }


    /**
     * @param Model $favor
     * @return bool
     * @throws Exception
     */
    protected function delete(Model $favor): bool
    {
        return $favor->delete();
    }

}
