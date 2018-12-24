<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string title
 * @property string description
 * @property string logo
 * @property bool is_verified
 * @property string email
 * @property string address
 */
class Seller extends Model
{

    protected $fillable = [
        'title', 'slug', 'description', 'logo', 'is_verified', 'email', 'address'
    ];

    protected $guarded = ['id'];


    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }


    /**
     * Get related seller telephones
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getTelephones()
    {
        return $this->hasMany('App\Models\SellerTelephone');
    }

}
