<?php
declare(strict_types = 1);

namespace App\Services\Image\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

interface ImageServiceContract
{
    public const MAX_IMG_WIDTH = 640;
    public const MAX_IMG_HEIGHT = 480;
    public const MAX_PREVIEW_WIDTH = 208;
    public const MAX_PREVIEW_HEIGHT = 156;

    /**
     * @param UploadedFile $item
     * @param int $id
     * @return bool
     */
    public function create(UploadedFile $item, int $id);


    /**
     * @param UploadedFile $item
     * @param int $key
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function update(int $key, UploadedFile $item, int $id);


    /**
     * @param Model $image
     * @return bool
     * @throws Exception
     */
    public function delete(Model $image);
}
