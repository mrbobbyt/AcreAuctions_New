<?php
declare(strict_types = 1);

namespace App\Services\Telephone\Contracts;

use Illuminate\Database\Eloquent\Model;
use Throwable;

interface TelServiceContract
{
    /**
     * Save telephones
     * @param int $key
     * @param int $value
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function create(int $key, int $value, int $id);


    /**
     * @param string $key
     * @param int $value
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function update(string $key, int $value, int $id);


    /**
     * Delete all related telephones
     * @param Model $model
     * @return mixed
     */
    public function delete(Model $model);
}