<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property string title
 * @property string description
 * @property string logo
 * @property string cover
 * @property string email
 * @property string address
 */
class SellerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tel = $this->getTelephones->map(function ($item) {
            return $item->number;
        })->toArray();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description ?? null,
            'logo' => $this->logo ? public_path().'/images/seller/' . $this->logo : null,
            'cover' => $this->cover ? public_path().'/images/seller/' . $this->cover : null,
            'telephones' => $tel,
            'email' => $this->email ?? null,
            'address' => $this->address ?? null,
        ];
    }
}
