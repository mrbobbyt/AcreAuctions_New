<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int seller_id
 * @property int number
 */
class SellerTelephone extends Model
{
    protected $fillable = ['seller_id', 'number'];

    protected $guarded = ['id'];

    public $timestamps = false;

}
