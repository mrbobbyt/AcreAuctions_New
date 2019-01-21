<?php
declare(strict_types = 1);

namespace App\Http\Resources;

use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property int inner_listing_id
 * @property int apn
 * @property string title
 * @property string subtitle
 * @property string slug
 * @property string description
 * @property bool is_featured
 * @property int seller_id
 * @property string utilities
 * @property string zoning
 * @property string zoning_desc
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
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'seller' => $this->seller->title,
            'utilities' => $this->getUtilities->name,
            'zoning' => $this->getZoning->name,
            'zoning_desc' => $this->zoning_desc,
            'property type' => $this->getPropertyType->name,

            'geo' => $this->geo,
            'road access' => $this->geo->getRoadAccess->name,
            'price' =>$this->price,
            'subdivision' =>$this->subdivision ?? null,
            'images' => $this->images,
            'docs' => $this->docs,
            'links' => $this->links,
            'videos' => $this->videos,
        ];
    }
}
