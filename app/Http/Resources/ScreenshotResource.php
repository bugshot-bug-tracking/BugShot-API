<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Http\Resources\Json\JsonResource;

class ScreenshotResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$path = "storage" . $this->url;
		$base64 = "";
		try {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);

            if($this->created_at > "2023-03-06 15:00:00") {
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $base64 = base64_encode($base64);
            } else {
                $base64 = base64_encode($data);
            }

		} catch (Exception $e) {
		}

		$screenshot = array(
			"id" => $this->id,
			"type" => "Screenshot",
			"attributes" => [
				"bug_id" => $this->bug_id,
				"client_id" => $this->client_id,
				"url" => $this->url,
				"position_x" => $this->position_x,
				"position_y" => $this->position_y,
				"web_position_x" => $this->web_position_x,
				"web_position_y" => $this->web_position_y,
				"base64" => $base64
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
