<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$status = array(
			"id" => $this->id,
			"type" => "Status",
			"attributes" => [
				"designation" => $this->designation,
				"order_number" => $this->order_number,
				"project_id" => $this->project_id
			]
		);	

		$header = $request->header();

		// Check if the response should contain the respective bugs
		if(array_key_exists('include-bugs', $header) && $header['include-bugs'][0] == "true") {
			$bugs = Auth::user()->bugs->where('status_id', $this->id);
			$status['attributes']['users'] = BugResource::collection($bugs);
		}

		return $status;
	}
}
