<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int listing_id
 * @property int fullsize_id
 * @property int preview_id
 */
class FullsizePreview extends Model
{
    protected $fillable = ['fullsize_id', 'preview_id', 'listing_id'];

    protected $guarded = ['id'];

    protected $hidden = ['fullsize_id', 'preview_id', 'listing_id'];

    protected $appends = ['full_path_fullsize', 'full_path_preview'];

    public $timestamps = false;


    /**
     * @return string
     */
    public function getFullPathFullsizeAttribute()
    {
        return Image::query()->where('id', $this->fullsize_id)->first();
    }


    /**
     * @return string
     */
    public function getFullPathPreviewAttribute()
    {
        return Image::query()->where('id', $this->preview_id)->first();
    }
}
