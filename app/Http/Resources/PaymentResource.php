<?php
declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property int user_id
 * @property int listing_id
 * @property int price
 * @property int total_price
 * @property string status
 * @property string transaction_id
 */
class PaymentResource extends JsonResource
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
            'status' => $this->status,
            'user_id' => $this->user_id,
            'listing_id' => $this->listing_id,
            'transaction_id' => $this->transaction_id,
            'price' => $this->price,
            'total_price' => $this->total_price,

            'created_at' => $this->created_at->toFormattedDateString(),
        ];
    }
}
