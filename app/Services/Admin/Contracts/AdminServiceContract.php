<?php
declare(strict_types = 1);

namespace App\Services\Admin\Contracts;

use App\Models\Seller;
use Illuminate\Database\Eloquent\Collection;

interface AdminServiceContract
{
    /**
     * Make seller verified
     * @param object $seller
     * @return bool
     */
    public function verifySeller(object $seller);

    /**
     * Returns all sellers.
     *
     * @return Seller[]|Collection
     */
    public function getAllSellers();
}
