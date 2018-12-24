<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int user_id
 * @property string title
 * @property string description
 * @property string logo
 * @property string cover
 * @property bool is_verified
 * @property string email
 * @property string address
 */
class Seller extends Model
{

    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'logo', 'cover', 'is_verified', 'email', 'address'
    ];

    protected $guarded = ['id'];


//    /**
//     * Get the route key for the model.
//     *
//     * @return string
//     */
//    public function getRouteKeyName()
//    {
//        return 'slug';
//    }


    /**
     * Get related seller telephones
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getTelephones()
    {
        return $this->hasMany('App\Models\SellerTelephone');
    }

    /**
     * Get head of company
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getHead()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
