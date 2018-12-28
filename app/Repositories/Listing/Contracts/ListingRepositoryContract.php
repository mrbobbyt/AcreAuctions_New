<?php
declare(strict_types = 1);

namespace App\Repositories\Listing\Contracts;

use Illuminate\Database\Eloquent\Model;

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

}
