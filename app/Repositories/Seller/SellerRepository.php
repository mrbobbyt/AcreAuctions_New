<?php
declare(strict_types = 1);

namespace App\Repositories\Seller;

use App\Http\Resources\SellerResource;
use App\Models\Email;
use App\Models\Seller;
use App\Models\Telephone;
use App\Repositories\Seller\Contracts\SellerRepositoryContract;
use Illuminate\Database\Eloquent\Model;

class SellerRepository implements SellerRepositoryContract
{

    /**
     * Find seller by url
     * @param string $slug
     * @return Model | bool
     */
    public function findBySlug(string $slug)
    {
        if ($seller = Seller::query()->where('slug', $slug)->first()) {
            return $seller;
        }

        return false;
    }


    /**
     * Find seller by id
     * @param int $id
     * @return Model | bool
     */
    public function findByPk(int $id)
    {
        if ($seller = Seller::query()->find($id)) {
            return $seller;
        }

        return false;
    }


    /**
     * Get related seller telephones
     * @param SellerResource $seller
     * @return array
     */
    public function getTelephones(SellerResource $seller): array
    {
        return $seller->telephones()
            ->where('entity_type', Telephone::TYPE_SELLER)
            ->get()->pluck('number')->toArray();
    }


    /**
     * Get related seller telephones
     * @param SellerResource $seller
     * @return array
     */
    public function getEmails(SellerResource $seller): array
    {
        return $seller->emails()
            ->where('entity_type', Email::TYPE_SELLER)
            ->get()->pluck('email')->toArray();
    }
}
