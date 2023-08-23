<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProjectSearchCollection  extends ResourceCollection
{
	/**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => ProjectSearchResource::collection($this->collection),
            'links' => [
                'self' => 'link-value',
            ],
        ];
    }
}
