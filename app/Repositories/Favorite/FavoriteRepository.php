<?php
declare(strict_types = 1);

namespace App\Repositories\Favorite;

use App\Models\Favorite;
use App\Repositories\Favorite\Contracts\FavoriteRepositoryContract;
use Illuminate\Database\Eloquent\Model;

class FavoriteRepository implements FavoriteRepositoryContract
{
    /**
     * @param array $data
     * @return Model | bool
     */
    public function findByPk(array $data)
    {
       $favor =  Favorite::query()->where([
           ['listing_id', $data['body']['listing_id']],
           ['user_id', $data['body']['user_id']],
       ])->first();

        return ($favor === null) ? false : $favor;
    }
}
