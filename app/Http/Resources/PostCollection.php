<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    private $pagination;

    public function __construct($resource)
    {
        if ($resource instanceof LengthAwarePaginator) {
            $resource->setPath(url()->current());
            $this->pagination = [
                'total' => $resource->total(),
                'count' => $resource->count(),
                'per_page' => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'total_pages' => $resource->lastPage(),
            ];
        }

        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'data' => PostResource::collection($this->collection),
        ];

        if ($this->pagination) {
            $data['pagination'] = $this->pagination;
        }

        return $data;
    }
}
