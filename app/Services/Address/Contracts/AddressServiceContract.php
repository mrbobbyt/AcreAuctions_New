<?php
declare(strict_types = 1);

namespace App\Services\Address\Contracts;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

interface AddressServiceContract
{
    /**
     * @param int $type
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function create(int $type, array $data, int $id);


    /**
     * @param int $type
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function update(int $type, array $data, int $id);


    /**
     * @param Model $model
     * @throws Exception
     */
    public function delete(Model $model);


    /**
     * @param int $type
     * @param int $id
     * @return bool|Model
     */
    public function find(int $type, int $id);
}
