<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int entity_id
 * @property int entity_type
 * @property string name
 */
class Image extends Model
{
    const TYPE_USER_AVATAR = 1;
    const TYPE_SELLER_LOGO = 2;
    const TYPE_SELLER_COVER = 3;
    const TYPE_LISTING = 4;

    protected $fillable = ['entity_id', 'entity_type', 'name'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'entity_id', 'entity_type'];

}
