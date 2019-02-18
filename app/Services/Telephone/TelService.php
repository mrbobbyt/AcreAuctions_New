<?php
declare(strict_types = 1);

namespace App\Services\Telephone;

use App\Models\Telephone;
use App\Services\Telephone\Contracts\TelServiceContract;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class TelService implements TelServiceContract
{
    /**
     * Save telephones
     * @param int $key
     * @param int $value
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function create(int $key, int $value, int $id): bool
    {
        $model = Telephone::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => $key,
            'number' => $value,
        ]);

        return $model->saveOrFail();
    }


    /**
     * @param string $key
     * @param int $value
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function update(string $key, int $value, int $id): bool
    {
        $type = Telephone::checkType($key);

        if ( $tel = $this->find($type, $id) ) {

            if (empty($value)) {
                return $tel->delete();
            }

            $tel->number = $value;
            return $tel->saveOrFail();
        }

        return $this->create($type, $value, $id);
    }


    /**
     * Delete all related telephones
     * @param Model $model
     * @return mixed
     */
    public function delete(Model $model)
    {
        return $model->telephones->each(function ($item, $key) {
            $item->delete();
        });
    }


    /**
     * @param int $key
     * @param int $id
     * @return bool|Model
     */
    public function find(int $key, int $id)
    {
        $tel = Telephone::query()->where([
            ['entity_type', $key],
            ['entity_id', $id],
        ])->first();

        return ($tel === null) ? false : $tel;
    }
}