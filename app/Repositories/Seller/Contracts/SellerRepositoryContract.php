<?php
declare(strict_types = 1);

namespace App\Repositories\Seller\Contracts;

use Illuminate\Database\Eloquent\Model;
use Exception;

interface SellerRepositoryContract
{

    /**
     * Find seller by url
     *
     * @param string $slug
     * @return Model
     * @throws Exception
     */
    public function findBySlug(string $slug);


    /**
     * Find seller by id
     *
     * @param int $id
     * @return Model | bool
     */
    public function findByPk(int $id);

}
