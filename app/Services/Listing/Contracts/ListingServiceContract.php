<?php
declare(strict_types = 1);

namespace App\Services\Listing\Contracts;

use Exception;
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


    /**
     * Update listing
     * @param int $id
     * @param array $data
     * @return Model
     * @throws Exception
     * @throws Throwable
     */
    public function update(array $data, int $id);


    /**
     * Delete listing and related models
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function delete(int $id);

}
