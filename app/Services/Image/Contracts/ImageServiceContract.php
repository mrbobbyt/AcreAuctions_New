<?php
declare(strict_types = 1);
# only for listings, because has fullsize and preview

namespace App\Services\Image\Contracts;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Throwable;

interface ImageServiceContract
{
    public const MAX_IMG_WIDTH = 640;
    public const MAX_IMG_HEIGHT = 480;
    public const MAX_PREVIEW_WIDTH = 208;
    public const MAX_PREVIEW_HEIGHT = 156;

    /**
     * @param UploadedFile|string $item
     * @param int $id
     * @param string $type
     * @param boolean $descImg
     * @return bool
     */
    public function create($item, int $id, string $type, bool $descImg = false);

    /**
     * @param string $imgUrl
     * @param int $id
     * @param string $type
     * @return bool
     */
    public function createImageFromUrl(string $imgUrl, int $id, string $type);


    /**
     * @param UploadedFile $item
     * @param int $key
     * @param string $type
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function update(int $key, UploadedFile $item, int $id, string $type);


    /**
     * @param Model $image
     * @return bool
     * @throws Exception
     */
    public function delete(Model $image);
}
