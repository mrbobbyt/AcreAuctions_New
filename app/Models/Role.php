<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 */
class Role extends Model
{
    protected $fillable = ['name'];

    protected $guarded = ['id'];

    public $timestamps = false;
}
