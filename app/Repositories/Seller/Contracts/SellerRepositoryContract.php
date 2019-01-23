<?php
declare(strict_types = 1);

namespace App\Repositories\Seller\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
