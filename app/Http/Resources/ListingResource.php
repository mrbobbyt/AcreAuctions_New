<?php
declare(strict_types = 1);

namespace App\Http\Resources;

use App\Repositories\Listing\ListingRepository;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property int inner_listing_id
 * @property int apn
 * @property string title
 * @property string slug
 * @property string description
 * @property bool is_featured
 * @property bool is_verified
 * @property int seller_id
 * @property int utilities
 * @property int zoning
 * @property string zoning_desc
 * @property int property_type
 */
class ListingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'listing_id' => $this->inner_listing_id,
            'apn' => $this->apn,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'is_featured' => $this->is_featured,
            'seller' => $this->sellerWithLogo,
            'utilities' => $this->getUtilities ? $this->getUtilities->name : null,
            'zoning' => $this->getZoning ? $this->getZoning->name : null,
            'zoning_desc' => $this->zoning_desc,
            'property_type' => $this->getPropertyType ? $this->getPropertyType->name : null,

            'geo' => ListingGeoResource::make( (new ListingRepository())->findGeoByPk($this->id) ),
            'price' => ListingPriceResource::make( (new ListingRepository())->findPriceByPk($this->id) ),
            'subdivision' =>$this->subdivision ?? null,
            'gallery' => $this->gallery,
            'docs' => $this->docs,
            'links' => $this->links,
            'videos' => $this->videos,
            'is_favorite' => $this->isFavorite(),
        ];
    }
}
