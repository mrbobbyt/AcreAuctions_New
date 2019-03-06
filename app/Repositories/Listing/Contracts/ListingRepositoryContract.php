<?php
declare(strict_types = 1);

namespace App\Repositories\Listing\Contracts;

use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use App\Repositories\Listing\Exceptions\ListingNotFoundException;

interface ListingRepositoryContract
{
    /**
     * Find seller by url
     * @param string $slug
     * @return Model
     * @throws ListingNotFoundException
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
     * @param int $key
     * @param int $id
     * @return Model | bool
     */
    public function findImage(int $key, int $id);


    /**
     * @param int $id
     * @return Model
     */
    public function findSubByPk(int $id);


    /**
     * @param int $key
     * @param int $id
     * @return Model | bool
     */
    public function findDoc(int $key, int $id);


    /**
     * @param int $type
     * @param int $key
     * @param int $id
     * @return Model | bool
     */
    public function findUrl(int $type, int $key, int $id);


    /**
     * @return array
     */
    public function getPropertyTypes();


    /**
     * @return array
     */
    public function getRoadAccess();


    /**
     * @return array
     */
    public function getUtilities();


    /**
     * @return array
     */
    public function getZoning();


    /**
     * @return array
     */
    public function getSaleTypes();
}
