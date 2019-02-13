<?php
declare(strict_types = 1);
# only for users and sellers, because they have only one image

namespace App\Services\Image;

use App\Models\Image;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use ImageConverter;
use App\Services\Image\Contracts\AvatarServiceContract;
use Throwable;
use File;

class AvatarService implements AvatarServiceContract
{
    /**
     * @param UploadedFile $item
     * @param int $id
     * @return bool
     * @throws Throwable
     */
    public function create(UploadedFile $item, int $id): bool
    {
        $img = ImageConverter::make($item);
        $name = str_random(20) .'_user_'. $id;
        $img->save(public_path().'/images/'.$name.'.jpg');

        $image = Image::query()->make()->fill([
            'entity_id' => $id,
            'entity_type' => Image::TYPE_USER_AVATAR,
            'name' => $name.'.jpg',
        ]);

        return $image->saveOrFail();
    }


    /**
     * @param UploadedFile $item
     * @param int $id
     * @param int $type
     * @return bool
     * @throws Throwable
     */
    public function update(UploadedFile $item, int $id, int $type): bool
    {
        if ($image = $this->find($id, $type)) {
            $this->delete($image);
        }

        return $this->create($item, $id);
    }


    /**
     * @param Model $image
     * @return bool
     * @throws Exception
     */
    public function delete(Model $image): bool
    {
        if (File::exists(public_path().'/images/'.$image->name)) {
            File::delete(public_path().'/images/'.$image->name);
        }

        return $image->delete();
    }


    /**
     * @param int $id
     * @param int $type
     * @return bool|Model
     */
    public function find(int $id, int $type)
    {
        $image = Image::query()->where([
                ['entity_id', $id],
                ['entity_type', $type === Image::TYPE_USER_AVATAR ?? Image::TYPE_SELLER_LOGO]
            ])->first();

        return ($image === null) ? false : $image;
    }
}
