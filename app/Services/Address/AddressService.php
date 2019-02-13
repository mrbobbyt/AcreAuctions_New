<?php
declare(strict_types = 1);

namespace App\Services\Address;

use App\Models\Address;
use App\Services\Address\Contracts\AddressServiceContract;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class AddressService implements AddressServiceContract
{
    /**
     * @param int $type
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function create(int $type, array $data, int $id): bool
    {
        $address = Address::query()->make();
        $address->entity_id = $id;
        $address->entity_type = $type;
        foreach ($data as $key => $value) {
            $address->$key = $value;
        }

        return $address->saveOrFail();
    }


    /**
     * @param int $type
     * @param array $data
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function update(int $type, array $data, int $id): bool
    {
        if ( $address = $this->find($type, $id) ) {
            foreach ($data as $key => $value) {
                $address->$key = $value;
            }
            return $address->saveOrFail();
        }

        return $this->create($type, $data, $id);
    }


    /**
     * @param Model $model
     * @throws Exception
     */
    public function delete(Model $model)
    {
        $model->address->delete();
    }


    /**
     * @param int $type
     * @param int $id
     * @return bool|Model
     */
    public function find(int $type, int $id)
    {
        $address = Address::query()->where([
            ['entity_type', $type],
            ['entity_id', $id],
        ])->first();

        return ($address === null) ? false : $address;
    }
}
