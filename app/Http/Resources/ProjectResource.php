<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		// Get and transform the stored project image back to a base64 if it exists
		// $base64 = NULL;
		// if($this->image != NULL) {
		// 	$path = "storage" . $this->image->url;
		// 	$data = file_get_contents($path);
		// 	$base64 = base64_encode($data);
		// }

		// Get and transform the stored company image back to a base64 if it exists
		// $companyBase64 = NULL;
		// if($this->company->image_path != NULL) {
		// 	$companyPath = "storage" . $this->company->image_path;
		// 	$companyData = file_get_contents($companyPath);
		// 	$companyBase64 = base64_encode($companyData);
		// }
		// $company = $this->company;

		$bugsTotal = $this->bugs->count();
		$bugsDone = $this->statuses->last()->bugs->count();

		return [
			"id" => $this->id,
			"type" => "Project",
			"attributes" => [
				"designation" => $this->designation,
				"url" => $this->url,
				// "base64" => $base64,
				"color_hex" => $this->color_hex,
				"company_id" => $this->company_id,
				"bugsTotal" => $bugsTotal,
				"bugsDone" => $bugsDone,
				"company_id" => $this->company_id
				// "bugs" => $this->bugs
			]
		];
	}
}
