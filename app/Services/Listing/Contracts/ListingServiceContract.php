<?php
declare(strict_types = 1);

namespace App\Services\Listing\Contracts;

use Illuminate\Database\Eloquent\Model;
use Throwable;

interface ListingServiceContract
{

    /**
     * @param array $data
     * @return Model
     * @throws Throwable
     */
    public function create(array $data);

}
