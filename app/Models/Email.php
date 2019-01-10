<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int entity_id
 * @property int entity_type
 * @property string email
 */
class Email extends Model
{
    const TYPE_SELLER = 1;

    protected $fillable = ['entity_id', 'entity_type', 'email'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'entity_id', 'entity_type'];

}
