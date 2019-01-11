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
class Doc extends Model
{
    const TYPE_LISTING = 1;

    protected $fillable = ['entity_id', 'entity_type', 'name'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'entity_id', 'entity_type'];
}
