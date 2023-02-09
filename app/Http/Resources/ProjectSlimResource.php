<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\ProjectUserRole;

class ProjectSlimResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
        $company = Company::find($this->company_id);
		$project = array(
			"id" => $this->id,
			"type" => "Project",
			"attributes" => [
				"creator" => new UserResource(User::find($this->user_id)),
				"designation" => $this->designation,
				"url" => $this->url,
                "company" => array(
					"id" => $company->id,
					"type" => "Company",
					"attributes" => [
						"creator" => new UserResource(User::find($company->user_id)),
						"designation" => $company->designation,
						"color_hex" => $company->color_hex,
					]
				),
				"color_hex" => $this->color_hex,
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at
			]
		);

		return $project;
	}
}
