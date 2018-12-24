<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int id
 * @property string fname
 * @property string lname
 * @property string password
 * @property string email
 * @property string rememberToken
 * @property int role
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'fname', 'lname', 'email', 'password', 'role'
    ];

    protected $guarded = ['id'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }


    /**
     * Get all reset tokens
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function resetTokens()
    {
        return $this->hasMany('App\Models\PasswordResets', 'email', 'email');
    }


    /**
     * Get user role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getRoleName()
    {
        return $this->belongsTo('App\Models\Role', 'role', 'id');
    }
}
