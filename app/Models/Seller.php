<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'user_id', 'title', 'slug', 'description', 'logo', 'cover', 'is_verified', 'address'
    ];

    protected $guarded = ['id'];


    /**
     * Get related seller telephones
     * @return HasMany
     */
    public function telephones()
    {
        return $this->hasMany('App\Models\Telephone', 'entity_id', 'id');
    }

    /**
     * Get head of company
     * @return BelongsTo
     */
    public function getHead()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    /**
     * Get related seller emails
     * @return HasMany
     */
    public function emails()
    {
        return $this->hasMany('App\Models\Email', 'entity_id', 'id');
    }
}
