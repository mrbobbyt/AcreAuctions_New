<?php
declare(strict_types = 1);

namespace App\Services\Admin;

use App\Models\Seller;
use App\Services\Admin\Contracts\AdminServiceContract;
use Illuminate\Database\Eloquent\Collection;

class AdminService implements AdminServiceContract
{
    /**
     * Make seller verified
     * @param object $seller
     * @return bool
     */
    public function verifySeller(object $seller): bool
    {
        $seller['is_verified'] = 1;

        return $seller->saveOrFail();
    }

    /**
     * Returns all sellers.
     *
     * @return Seller[]|Collection
     */
    public function getAllSellers()
    {
        return Seller::all();
    }
}
