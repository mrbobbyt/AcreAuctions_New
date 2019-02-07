<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int price
 * @property int monthly_payment
 * @property int processing_fee
 * @property int percentage_rate
 * @property int financial_term
 * @property int taxes
 */
class ListingPriceResource extends JsonResource
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
            'price' => $this->price,
            'sale_type' =>$this->getSaleType ? $this->getSaleType->name : null,
            'monthly_payment' => $this->monthly_payment,
            'processing_fee' => $this->processing_fee,
            'percentage_rate' => $this->percentage_rate,
            'financial_term' => $this->financial_term,
            'taxes' => $this->taxes,
        ];
    }
}
