<?php

namespace App\Services\Seller;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use App\Services\Seller\Contracts\SellerServiceContract;
use Throwable;

class SellerService implements SellerServiceContract
{

    protected $model;
    protected $sellerRepo;

    public function __construct(Seller $seller, SellerRepositoryContract $sellerRepo)
    {
        $this->model = $seller;
        $this->sellerRepo = $sellerRepo;
    }


    /**
     * @param array $data
     * @return Model
     * @throws Throwable
     */
    public function create(array $data): Model
    {
        $data['slug'] = $this->makeUrl($data['title']);
        $seller = $this->model->query()->make()->fill($data);
        $seller->saveOrFail();

        return $seller;
    }


    /**
     * Return slug created from title
     *
     * @param string $title
     * @return string
     */
    public function makeUrl(string $title): string
    {
        $str = preg_replace('/^\PL+|\PL\z/', '', $title);
        return str_replace( ' ', '_', $str);
    }
}