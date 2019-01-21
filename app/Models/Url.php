<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int entity_id
 * @property int entity_type
 * @property string name
 * @property string desc
 */
class Url extends Model
{
    const TYPE_LISTING_LINK = 1;
    const TYPE_LISTING_YOUTUBE = 2;

    protected $fillable = ['entity_id', 'entity_type', 'name', 'desc'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'entity_id', 'entity_type'];
}
