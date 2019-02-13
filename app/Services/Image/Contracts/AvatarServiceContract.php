<?php
declare(strict_types = 1);
# only for users and sellers, because they have only one image

namespace App\Services\Image\Contracts;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Throwable;

interface AvatarServiceContract
{
    /**
     * @param UploadedFile $item
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function create(UploadedFile $item, int $id);


    /**
     * @param UploadedFile $item
     * @param int $id
     * @param int $type
     * @return bool
     * @throws Throwable
     */
    public function update(UploadedFile $item, int $id, int $type);


    /**
     * @param Model $image
     * @return bool
     * @throws Exception
     */
    public function delete(Model $image);


    /**
     * @param int $id
     * @param int $type
     * @return bool|Model
     */
    public function find(int $id, int $type);
}
