<?php

namespace App\Http\Resources;

use Exception;
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
		if (array_key_exists('include-attachment-base64', $header) && $header['include-attachment-base64'][0] == "true") {
			$path = "storage" . $this->url;
			try {
				$type = pathinfo($path, PATHINFO_EXTENSION);
				$data = file_get_contents($path);
				$dataSubstr = substr(base64_encode($data), 0, 1);

				if($dataSubstr == "/") {
					$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
					$base64 = base64_encode($base64);
				} else {
					$base64 = base64_encode($data);
				}
				$attachment['attributes']['base64'] = $base64;
			} catch (Exception $e) {
				$attachment['attributes']['base64'] = null;
			}
		}

		return $attachment;
	}
}
