<?php
declare(strict_types = 1);

namespace App\Http\Resources;

use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property string title
 * @property string subtitle
 * @property string slug
 * @property string description
 * @property bool is_featured
 * @property int seller_id
 *
 * @property string size_type
 * @property string state
 * @property string county
 * @property string city
 * @property string address
 * @property double longitude
 * @property double latitude
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
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'images' => app(ListingRepositoryContract::class)->getImageNames($this) ?? null,
            'description' => $this->description,
            'seller' => $this->seller->title,

            'size_type' => $this->geo->size_type,
            'state' => $this->geo->state,
            'county' => $this->geo->county,
            'city' => $this->geo->city,
            'address' => $this->geo->address,
            'longitude' => $this->geo->longitude,
            'latitude' => $this->geo->latitude,
        ];
    }
}
