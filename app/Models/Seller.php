<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int id
 * @property int user_id
 * @property string title
 * @property string description
 * @property bool is_verified
 * @property string address
 */
class Seller extends Model
{
    protected $fillable = [
        'user_id', 'title', 'slug', 'description', 'is_verified', 'address'
    ];

    protected $guarded = ['id'];


    /**
     * Get related seller telephones
     * @return HasMany
     */
    public function telephones()
    {
        return $this->hasMany(Telephone::class, 'entity_id', 'id');
    }


    /**
     * Get head of company
     * @return BelongsTo
     */
    public function getHead()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Get related seller emails
     * @return HasMany
     */
    public function emails()
    {
        return $this->hasMany(Email::class, 'entity_id', 'id');
    }


    /**
     * Get related seller logo
     * @return HasOne
     */
    public function avatar()
    {
        return $this->hasOne(Image::class,'entity_id', 'id')
            ->where('entity_type', Image::TYPE_SELLER_LOGO);
    }
}
