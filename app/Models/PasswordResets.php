<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string email
 * @property string token
 */
class PasswordResets extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'password_resets';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'token', 'created_at'
    ];


    public $timestamps = false;


    /**
     * Get belongs user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'email', 'email');
    }

}
