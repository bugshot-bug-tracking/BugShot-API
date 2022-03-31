<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

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
		$company = array(
			"id" => $this->id,
			"type" => "Company",
			"attributes" => [
				"creator" => new UserResource(User::find($this->user_id)),
				"designation" => $this->designation,
				"color_hex" => $this->color_hex,
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at
			]
		);
		
		$header = $request->header();
	
		// Check if the response should contain the respective projects
		if(array_key_exists('include-projects', $header) && $header['include-projects'][0] == "true") {
			$projects = Auth::user()->projects->where('company_id', $this->id);
			$company['attributes']['projects'] = ProjectResource::collection($projects);
		}
		
		// Check if the response should contain the respective company users
		if(array_key_exists('include-company-users', $header) && $header['include-company-users'][0] == "true") {
			$users = $this->users;
			$company['attributes']['users'] = UserResource::collection($users);
		}

		// Check if the response should contain the respective company image
		if(array_key_exists('include-company-image', $header) && $header['include-company-image'][0] == "true") {
			$image = $this->image;
			$company['attributes']['image'] = new ImageResource($image);
		}

		return $company;
	}
}
