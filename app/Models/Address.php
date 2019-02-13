<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int entity_id
 * @property int entity_type
 * @property string address_first
 * @property string address_second
 * @property string city
 * @property string state
 * @property int zip
 */
class Address extends Model
{
    const TYPE_USER = 1;

    protected $fillable = ['entity_id', 'entity_type', 'address_first', 'address_second', 'city', 'state', 'zip'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'entity_id', 'entity_type'];

}
