<?php
declare(strict_types = 1);

namespace App\Services\Admin;

use App\Services\Admin\Contracts\AdminServiceContract;

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
}