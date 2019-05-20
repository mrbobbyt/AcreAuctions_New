<?php
declare(strict_types = 1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property int id
 * @property string title
 * @property string description
 * @property bool allow_comments
 * @property bool allow_somethings
 */
class PostResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'allow_comments' => $this->allow_comments,
            'allow_somethings' => $this->allow_somethings,
            'created_at' => $this->created_at->toDateString(),
        ];
    }
}
