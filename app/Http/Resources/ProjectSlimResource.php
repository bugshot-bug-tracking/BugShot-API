<?php

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectSlimResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $project = array(
            "id" => $this->id,
            "type" => "Project",
            "attributes" => [
                "designation" => $this->designation,
                "url" => $this->url,
                "color_hex" => $this->color_hex,
                "created_at" => $this->created_at,
                "updated_at" => $this->updated_at
            ]
        );

        return $project;
    }
}
