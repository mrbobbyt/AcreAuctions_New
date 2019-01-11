<?php
declare(strict_types = 1);

namespace App\Http\Resources;

use App\Repositories\Listing\Contracts\ListingRepositoryContract;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property int apn
 * @property string title
 * @property string subtitle
 * @property string slug
 * @property string description
 * @property bool is_featured
 * @property int seller_id
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
            'apn' => $this->apn,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'images' => app(ListingRepositoryContract::class)->getImageNames($this) ?? null,
            'description' => $this->description,
            'seller' => $this->seller->title,

            'size_type' => $this->geo->acreage,
            'state' => $this->geo->state,
            'county' => $this->geo->county,
            'city' => $this->geo->city,
            'address' => $this->geo->address,
            'longitude' => $this->geo->longitude,
            'latitude' => $this->geo->latitude,

            'docs' => app(ListingRepositoryContract::class)->getDocNames($this) ?? null,
        ];
    }
}
