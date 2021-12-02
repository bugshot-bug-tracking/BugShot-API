<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		// $base64 = NULL;
		// if($this->image != NULL) {
		// 	$path = "storage" . $this->image->url;
		// 	$data = file_get_contents($path);
		// 	$base64 = base64_encode($data);
		// }

		// Check if the response should contain the respective projects
		$header = $request->header();
		$projects = array_key_exists('include-projects', $header) && $header['include-projects'][0] == "true" ? $this->projects : [];

		return [
			"id" => $this->id,
			"type" => "Company",
			"attributes" => [
				"designation" => $this->designation,
				"color_hex" => $this->color_hex,
				"projects" => ProjectResource::collection($projects)
				// "users" => $this->users
			]
		];
	}
}
