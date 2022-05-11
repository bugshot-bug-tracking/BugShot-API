<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BugResource;

class ProjectMarkerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $markers = collect();
        foreach($this->screenshots as $screenshot) {
            $markers = $markers->concat($screenshot->markers);
        }

		return [
			"id" => $this->id,
			"type" => "ProjektMarker",
			"attributes" => [
				"designation" => $this->designation,
                'priority_id' => $this->priority_id,
                'status' => [
                    'id' => $this->status_id,
                    'permanent' =>  $this->status->permanent
                ],
                'markers' => MarkerResource::collection($markers)
			]
		];
    }
}

