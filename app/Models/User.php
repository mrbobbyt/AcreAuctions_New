<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    const ROLE_ADMIN = 1;
    const ROLE_SELLER = 2;
    const ROLE_BUYER = 3;

    protected $fillable = ['fname', 'lname', 'email', 'password', 'role'];

    protected $guarded = ['id'];

    protected $hidden = ['password', 'remember_token'];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        // TODO: Implement getJWTIdentifier() method.
        return $this->getKey();
    }


    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims()
    {
        // TODO: Implement getJWTCustomClaims() method.
        return [];
    }


    /**
     * Get all reset tokens
     * @return HasMany
     */
    public function resetTokens()
    {
        return $this->hasMany('App\Models\PasswordResets', 'email', 'email');
    }


    /**
     * Get user role
     * @return BelongsTo
     */
    public function getRoleName()
    {
        return $this->belongsTo('App\Models\Role', 'role', 'id');
    }


    /**
     * Create full name from first and last name
     * @return string
     */
    public function getFullName()
    {
        return $this->fname . ' ' . $this->lname;
    }


    /**
     * Get user avatar
     * @return HasOne
     */
    public function avatar()
    {
        return $this->hasOne('App\Models\Image', 'entity_id', 'id')
            ->where('entity_type', Image::TYPE_USER_AVATAR);
    }


    /**
     * Get seller
     * @return HasOne
     */
    public function seller()
    {
        return $this->hasOne('App\Models\Seller', 'user_id');
    }


    public function getAllFavorites()
    {
        return $this->belongsToMany('App\Models\Listing', 'favorites', 'user_id', 'listing_id');
    }


    /**
     * Get related seller telephones
     * @return HasMany
     */
    public function telephones()
    {
        return $this->hasMany('App\Models\Telephone', 'entity_id', 'id')
            ->whereIn('entity_type', [Telephone::TYPE_USER_PHONE, Telephone::TYPE_USER_FAX, Telephone::TYPE_USER_TOLL_FREE]);
    }


    /**
     * Get user address
     * @return HasOne
     */
    public function address()
    {
        return $this->hasOne('App\Models\Address', 'entity_id')
            ->where('entity_type', Address::TYPE_USER);
    }
}
