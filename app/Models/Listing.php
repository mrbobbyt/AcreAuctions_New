<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int id
 * @property string title
 * @property string subtitle
 * @property string slug
 * @property string description
 * @property bool is_featured
 * @property int seller_id
 */
class Listing extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'slug', 'description', 'is_featured', 'seller_id'
    ];

    protected $guarded = ['id'];

    public $timestamps = true;


    /**
     * Get related geo listing
     * @return HasOne
     */
    public function geo()
    {
        return $this->hasOne('App\Models\ListingGeo');
    }


    /**
     * Get related images
     * @return HasMany
     */
    public function images()
    {
        return $this->hasMany('App\Models\Image', 'entity_id', 'id')
            ->where('entity_type', Image::TYPE_LISTING);
    }


    /**
     * Get related seller
     * @return BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo('App\Models\Seller');
    }

}
