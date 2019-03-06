<?php
declare(strict_types = 1);

namespace App\Models;

use App\Traits\ModelBuilderScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int id
 * @property int inner_listing_id
 * @property int apn
 * @property string title
 * @property string slug
 * @property string description
 * @property bool is_featured
 * @property int status
 * @property int seller_id
 * @property int utilities
 * @property int zoning
 * @property string zoning_desc
 * @property int property_type
 */
class Listing extends Model
{
    use ModelBuilderScopes;

    protected $fillable = [
        'inner_listing_id', 'apn', 'title', 'slug', 'description', 'is_featured', 'status',
        'seller_id', 'utilities', 'zoning', 'zoning_desc', 'property_type'
    ];

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];

    public $timestamps = true;


    /**
     * Get related geo listing
     * @return HasOne
     */
    public function geo()
    {
        return $this->hasOne(ListingGeo::class);
    }


    /**
     * Get related images
     * @return HasMany
     */
    public function images()
    {
        return $this->hasMany(Image::class, 'entity_id', 'id')
            ->where('entity_type', Image::TYPE_LISTING);
    }


    /**
     * Get related images - fullsize and preview
     * @return HasMany
     */
    public function gallery()
    {
        return $this->hasMany(FullsizePreview::class, 'listing_id', 'id');
    }

    /**
     * Get related seller
     * @return BelongsTo
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }


    /**
     * Get related price listing
     * @return HasOne
     */
    public function price()
    {
        return $this->hasOne(ListingPrice::class);
    }


    /**
     * Get related documents
     * @return HasMany
     */
    public function docs()
    {
        return $this->hasMany(Doc::class, 'entity_id', 'id')
            ->where('entity_type', Doc::TYPE_LISTING);
    }


    /**
     * Get listing utility
     * @return BelongsTo
     */
    public function getUtilities()
    {
        return $this->belongsTo(Utility::class, 'utilities', 'id');
    }


    /**
     * Get listing zoning
     * @return BelongsTo
     */
    public function getZoning()
    {
        return $this->belongsTo(Zoning::class, 'zoning', 'id');
    }


    /**
     * Get listing subdivision
     * @return HasOne
     */
    public function subdivision()
    {
        return $this->hasOne(Subdivision::class);
    }


    /**
     * Get related urls
     * @return HasMany
     */
    public function links()
    {
        return $this->hasMany(Url::class, 'entity_id', 'id')
            ->where('entity_type', Url::TYPE_LISTING_LINK);
    }


    /**
     * Get related videos
     * @return HasMany
     */
    public function videos()
    {
        return $this->hasMany(Url::class, 'entity_id', 'id')
            ->where('entity_type', Url::TYPE_LISTING_YOUTUBE);
    }


    /**
     * Get listing property types
     * @return BelongsTo
     */
    public function getPropertyType()
    {
        return $this->belongsTo(PropertyType::class, 'property_type', 'id');
    }


    /**
     * Get seller info with logo for search listing
     * @return BelongsTo
     */
    public function sellerWithLogo()
    {
        return $this->seller()->select('id', 'title', 'slug')->with('avatar');
    }


    /**
     * Get all users that make this listing favorite
     * @return BelongsToMany
     */
    public function favorite()
    {
        return $this->belongsToMany('App\Models\User', 'favorites', 'listing_id', 'user_id');
    }


    /**
     * Check is user make this listing favorite
     * @return bool
     */
    public function isFavorite(): bool
    {
        if(\JWTAuth::user()) {
            return $this->favorite()
                ->where('user_id', \JWTAuth::user()->getJWTIdentifier())
                ->exists();
        }

        return false;
    }


    /**
     * Get listing status
     * @return BelongsTo
     */
    public function getStatus()
    {
        return $this->belongsTo(ListingStatus::class, 'status', 'id');
    }
}
