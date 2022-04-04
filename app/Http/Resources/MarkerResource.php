<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MarkerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
		return [
			"id" => $this->id,
			"type" => "Marker",
			"attributes" => [
				"screenshot_id" => $this->screenshot_id,
				"position_x" => $this->position_x,
                "position_y" => $this->position_y,
                "web_position_x" => $this->web_position_x,
                "web_position_y" => $this->web_position_y,
                "target_x" => $this->target_x,
                "target_y" => $this->target_y,
                "target_height" => $this->target_height,
                "target_width" => $this->target_width,
                "scroll_x" => $this->scroll_x,
                "scroll_y" => $this->scroll_y,
                "screenshot_height" => $this->screenshot_height,
                "screenshot_width" => $this->screenshot_width,
                "target_full_selector" => $this->target_full_selector,
                "target_short_selector" => $this->target_short_selector,
                "target_html" => $this->target_html,
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at
			]
		];
    }
}
