<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScreenshotInterfaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
		$screenshot = array(
			"id" => $this->id,
			"type" => "Screenshot",
			"attributes" => [
				"bug_id" => $this->bug_id,
				"client_id" => $this->client_id,
				"url" => $this->url,
				"position_x" => $this->position_x,
				"position_y" => $this->position_y,
				"base64" => $this->base64
			]
		);

		$header = $request->header();

		// Check if the response should contain the respective screenshots
		if(array_key_exists('include-markers', $header) && $header['include-markers'][0] == "true") {
			$markers = $this->markers;
			$screenshot['attributes']['markers'] = MarkerResource::collection($markers);
		}

		return $screenshot;
    }
}
