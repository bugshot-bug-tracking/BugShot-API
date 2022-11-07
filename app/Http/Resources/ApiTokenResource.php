<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApiTokenResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
        // Check the model type of the api token
        switch ($this->api_tokenable_type) {
            case 'project':
                $resource = new ProjectResource($this->apiTokenable);
                break;
        }

        $apiToken = array(
			"id" => $this->id,
			"type" => "ApiToken",
			"attributes" => [
                "api_tokenable" => $resource,
                "token" => $this->token,
                "created_at" => $this->created_at,
                "updated_at" => $this->updated_at
			]
		);

        return $apiToken;
	}
}
