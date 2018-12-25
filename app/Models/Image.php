<?php

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

    protected $fillable = ['entity_id', 'entity_type', 'name'];

    protected $guarded = ['id'];

}
