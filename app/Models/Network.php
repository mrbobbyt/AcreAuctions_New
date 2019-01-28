<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 */
class Network extends Model
{
    protected $fillable = ['name'];

    protected $guarded = ['id'];

    public $timestamps = false;
}
