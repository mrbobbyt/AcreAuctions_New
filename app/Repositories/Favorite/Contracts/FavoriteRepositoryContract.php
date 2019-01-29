<?php
declare(strict_types = 1);

namespace App\Repositories\Favorite\Contracts;

use Illuminate\Database\Eloquent\Model;

interface FavoriteRepositoryContract
{
    /**
     * @param array $data
     * @return Model
     */
    public function findByPk(array $data);
}