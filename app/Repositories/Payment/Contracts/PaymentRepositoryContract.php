<?php
declare(strict_types = 1);

namespace App\Repositories\Payment\Contracts;

interface PaymentRepositoryContract
{
    /**
     * @param string $listingId
     * @return mixed
     */
    public function findByListingId(string $listingId);

    /**
     * @return mixed
     */
    public function getNewToken();
}
