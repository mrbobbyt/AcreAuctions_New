<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int listing_id
 * @property string name
 * @property int yearly_dues
 */
class Subdivision extends Model
{
    protected $fillable = ['listing_id', 'name', 'yearly_dues'];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at', 'listing_id'];

}
