<?php
declare(strict_types = 1);

namespace App\Repositories\Listing\Contracts;

use App\Http\Resources\ListingResource;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Exceptions\JWTException;

interface ListingRepositoryContract
{

    /**
     * Find seller by url
     * @param string $slug
     * @return Model | bool
     */
    public function findBySlug(string $slug);


    /**
     * Find seller by id
     * @param int $id
     * @return Model | bool
     */
    public function findByPk(int $id);


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
     */
    public function findSellerById();


    /**
     * Find geo listing by listing id
     * @param int $id
     * @return Model
     * @throws Exception
     */
    public function findGeoByPk(int $id);

}
