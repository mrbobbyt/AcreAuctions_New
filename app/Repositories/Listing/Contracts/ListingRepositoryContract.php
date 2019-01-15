<?php
declare(strict_types = 1);

namespace App\Repositories\Listing\Contracts;

use App\Http\Resources\ListingResource;
use App\Repositories\User\Exceptions\NoPermissionException;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

interface ListingRepositoryContract
{
    /**
     * Find seller by url
     * @param string $slug
     * @return Model
     */
    public function findBySlug(string $slug);


    /**
     * Find seller by id
     * @param int $id
     * @return Model
     */
    public function findByPk(int $id);


    /**
     * Check existing Listing by title
     * @param string $title
     * @return bool
     */
    public function findByTitle(string $title);


    /**
     * Get related images
     * @param ListingResource $listing
     * @return mixed
     */
    public function getImageNames(ListingResource $listing);


    /**
     * Get seller id
     * @return int
     * @throws JWTException
     * @throws TokenInvalidException
     * @throws TokenExpiredException
     */
    public function findSellerById();


    /**
     * Find geo listing by listing id
     * @param int $id
     * @return Model
     */
    public function findGeoByPk(int $id);


    /**
     * Find price listing by listing id
     * @param int $id
     * @return Model
     */
    public function findPriceByPk(int $id);


    /**
     * Check user`s permission to make action
     * @param $user
     * @param int $id
     * @return bool
     * @throws NoPermissionException
     */
    public function checkPermission($user, int $id);


    /**
     * @param int $key
     * @param int $id
     * @return Model | bool
     */
    public function findImage(int $key, int $id);
}
