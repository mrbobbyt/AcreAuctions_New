<?php
declare(strict_types = 1);

namespace App\Http\Resources;

use App\Repositories\Seller\Contracts\SellerRepositoryContract;
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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'head' => $this->getHead->getFullName(),
            'title' => $this->title,
            'description' => $this->description ?? null,
            'logo' => $this->logo ? public_path().'/images/seller/' . $this->logo->name : null,
            'cover' => $this->cover ? public_path().'/images/seller/' . $this->cover->name : null,
            'telephones' => app(SellerRepositoryContract::class)->getTelephones($this) ?? null,
            'email' =>app(SellerRepositoryContract::class)->getEmails($this) ?? null,
            'address' => $this->address ?? null,
        ];
    }
}
