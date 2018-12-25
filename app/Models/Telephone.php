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
    protected $fillable = ['entity_id', 'entity_type', 'number'];

    protected $guarded = ['id'];

}
