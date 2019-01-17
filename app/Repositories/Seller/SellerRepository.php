<?php
declare(strict_types = 1);

namespace App\Repositories\Seller;

use App\Http\Resources\SellerResource;
use App\Models\Email;
use App\Models\Seller;
use App\Models\Telephone;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Repositories\Seller\Exceptions\SellerNotVerifiedException;
use App\Repositories\User\Contracts\UserRepositoryContract;
use App\Repositories\User\Exceptions\NoPermissionException;
use App\Services\User\Contracts\UserServiceContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;

class SellerRepository implements SellerRepositoryContract
{
    protected $model;
    protected $userService;
    protected $userRepo;

    public function __construct(Seller $seller, UserServiceContract $userService, UserRepositoryContract $userRepo)
    {
        $this->model = $seller;
        $this->userService = $userService;
        $this->userRepo = $userRepo;
    }


    /**
     * Find seller by url
     * @param string $slug
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findBySlug(string $slug): Model
    {
        return $this->model::query()->where('slug', $slug)->firstOrFail();
    }


    /**
     * Check existing seller by title
     * @param string $title
     * @return bool
     */
    public function findByTitle(string $title): bool
    {
        return $this->model::query()->where('title', $title)->exists();
    }


    /**
     * Find seller by id
     * @param int $id
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findByPk(int $id): Model
    {
        return $this->model::query()->findOrFail($id);
    }


    /**
     * Check if seller is not verified OR user is authenticate AND not an admin OR company head
     * @param Model $seller
     * @return bool
     * @throws SellerNotVerifiedException
     * @throws JWTException
     */
    public function checkVerification(Model $seller): bool
    {
        if ($seller->is_verified ||
                ($this->userRepo->checkToken() &&
                    ($this->userRepo->isAdmin() || $seller->user_id === $this->userRepo->getId())
                )
        ) {
            return true;
        }

        throw new SellerNotVerifiedException();
    }


    /**
     * Check user`s permission to make action
     * @param int $id
     * @return bool
     * @throws JWTException
     * @throws ModelNotFoundException
     * @throws NoPermissionException
     */
    public function checkPermission(int $id): bool
    {
         return $this->userRepo->checkPermission( $this->findByPk($id)->user_id );
    }


    /**
     * @param int $key
     * @param int $id
     * @return Model | bool
     */
    public function findEmail(int $key, int $id)
    {
        $email = Email::query()->where([
            ['id', $key],
            ['entity_id', $id],
            ['entity_type', Email::TYPE_SELLER],
        ])->first();

        return ($email === null) ? false : $email;
    }


    /**
     * @param int $key
     * @param int $id
     * @return Model | bool
     */
    public function findTelephone(int $key, int $id)
    {
        $tel = Telephone::query()->where([
            ['id', $key],
            ['entity_id', $id],
            ['entity_type', Telephone::TYPE_SELLER],
        ])->first();

        return ($tel === null) ? false : $tel;
    }

}
