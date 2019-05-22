<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int listing_id
 * @property int post_id
 * @property int fullsize_id
 * @property int preview_id
 */
class FullsizePreview extends Model
{
    protected $fillable = ['fullsize_id', 'preview_id', 'listing_id', 'post_id'];

    protected $guarded = ['id'];

    protected $hidden = ['listing_id', 'post_id'];

    protected $appends = ['fullsize', 'preview'];

    public $timestamps = false;


    /**
     * @return string
     */
    public function getFullsizeAttribute()
    {
        return '/images/fullsize/'. Image::query()->where('id', $this->fullsize_id)->first()->name;
    }


    /**
     * @return string
     */
    public function getPreviewAttribute()
    {
        return '/images/preview/'. Image::query()->where('id', $this->preview_id)->first()->name;
    }
}
