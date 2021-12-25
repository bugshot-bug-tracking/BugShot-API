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
		$attachment = array(
			"id" => $this->id,
			"type" => "Attachment",
			"attributes" => [
				"bug_id" => $this->bug_id,
				"designation" => $this->designation
			]
		);

		$header = $request->header();
		
		// Check if the response should contain the base64
		if(array_key_exists('include-attachment-base64', $header) && $header['include-attachment-base64'][0] == "true") {
			$path = "storage" . $this->url;
			$data = file_get_contents($path);
			$base64 = base64_encode($data);
			$attachment['attributes']['base64'] = $base64;
		}

		return $attachment;
	}
}
