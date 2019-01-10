<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int entity_id
 * @property int entity_type
 * @property int number
 */
class Telephone extends Model
{
    const TYPE_USER = 1;
    const TYPE_SELLER = 2;

    protected $fillable = ['entity_id', 'entity_type', 'number'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'entity_id', 'entity_type'];

}
