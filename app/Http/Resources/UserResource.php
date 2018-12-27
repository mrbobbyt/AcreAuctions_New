<?php
declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property string fname
 * @property string lname
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
            'fname' => $this->fname,
            'lname' => $this->lname,
            'email' => $this->email,
            'role' => $this->getRoleName->name ?? null,
            'avatar' => $this->avatar ? get_image_path('User', $this->avatar->name) : null
        ];
    }
}
