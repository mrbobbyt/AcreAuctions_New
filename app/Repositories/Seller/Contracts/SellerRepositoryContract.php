<?php
declare(strict_types = 1);

namespace App\Repositories\Seller\Contracts;

use App\Http\Resources\SellerResource;
use App\Repositories\Seller\Exceptions\SellerNotVerifiedException;
use App\Repositories\User\Exceptions\NoPermissionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

interface SellerRepositoryContract
{
    /**
     * Find seller by url
     * @param string $slug
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findBySlug(string $slug);


    /**
     * Find seller by title
     * @param string $title
     * @return bool
     */
    public function findByTitle(string $title);


    /**
     * Find seller by id
     * @param int $id
     * @return Model
     * @throws ModelNotFoundException
     */
    public function findByPk(int $id);


    /**
     * Check if seller is not verified OR user is authenticate AND not an admin OR company head
     * @param Model $seller
     * @return bool
     * @throws SellerNotVerifiedException
     * @throws JWTException
     * @throws TokenInvalidException
     */
    public function checkVerification(Model $seller);


    /**
     * Check user`s permission to make action
     *
     * @param int $id
     * @return bool
     * @throws JWTException
     * @throws ModelNotFoundException
     * @throws NoPermissionException
     */
    public function checkPermission(int $id);


    /**
     * @param int $key
     * @param int $id
     * @return Model
     */
    public function findEmail(int $key, int $id);


    /**
     * @param int $key
     * @param int $id
     * @return Model
     */
    public function findTelephone(int $key, int $id);
}
