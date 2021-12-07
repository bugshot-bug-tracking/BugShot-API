<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
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
		// Check if the response should contain the respective projects
		$header = $request->header();
		$projects = array_key_exists('include-projects', $header) && $header['include-projects'][0] == "true" ? Auth::user()->projects->where('company_id', $this->id) : [];
		
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
