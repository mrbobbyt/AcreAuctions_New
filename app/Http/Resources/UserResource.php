<?php
declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property string fname
 * @property string lname
 * @property string payment_token
 * @property string email
 */
class UserResource extends JsonResource
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
            'slug' => url('/') . '/user/' . $this->id,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'payment_token' => $this->payment_token,
            'email' => $this->email,
            'role' => $this->userRole->name ?? null,
            'avatar' => $this->avatar,
            'telephones' => $this->telephones,
            'address' => $this->address,
        ];
    }
}
