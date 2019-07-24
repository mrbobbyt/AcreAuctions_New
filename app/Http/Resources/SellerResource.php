<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property string title
 * @property string email
 * @property string address
 */
class SellerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'head' => $this->getHead ? $this->getHead->getFullName() : null,
            'title' => $this->title,
            'telephones' => $this->telephones,
            'emails' => $this->emails,
            'address' => $this->address,
        ];
    }
}
