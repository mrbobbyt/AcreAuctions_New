<?php
declare(strict_types = 1);

namespace App\Repositories\Seller;

use App\Models\Email;
use App\Models\Seller;
use App\Models\Telephone;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SellerRepository implements SellerRepositoryContract
{
    protected $model;

    public function __construct(Seller $seller)
    {
        $this->model = $seller;
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
