<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		if($this->url != NULL) {
			$path = "storage" . $this->url;
			$data = file_get_contents($path);
			$base64 = base64_encode($data);
		}

		return [
			"id" => $this->id,
			"type" => "Attachment",
			"attributes" => [
				"bug_id" => $this->bug_id,
				"designation" => $this->designation,
				"url" => $this->url,
				"base64" => $base64
			]
		];
	}
}
