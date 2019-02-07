<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int acreage
 * @property string state
 * @property string county
 * @property string city
 * @property string address
 * @property int zip
 * @property double longitude
 * @property double latitude
 */
class ListingGeoResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'acreage' => $this->acreage,
            'state' => $this->state,
            'county' => $this->county,
            'city' => $this->city,
            'address' => $this->address,
            'zip' => $this->zip,
            'road_access' => $this->getRoadAccess ? $this->getRoadAccess->name : null,
            'longitude' => $this->longitude,
            'latitude' => $this->latitude,
        ];

    }
}
