<?php
declare(strict_types = 1);

namespace App\Services\Admin\Contracts;

interface AdminServiceContract
{
    /**
     * Make seller verified
     * @param object $seller
     * @return bool
     */
    public function verifySeller(object $seller);
}