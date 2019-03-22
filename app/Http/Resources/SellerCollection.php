<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SellerCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return SellerResource::collection($this->collection);
    }
}
