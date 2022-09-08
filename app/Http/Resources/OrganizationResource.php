<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class OrganizationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
		return [
			'id' => $this->id,
			'type' => 'Organization',
			'attributes' => [
				'creator' => new UserResource(User::find($this->user_id)),
				'designation' => $this->designation,
				// 'street' => $this->street,
				// 'housenumber' => $this->housenumber,
				// 'state' => $this->state,
				// 'city' => $this->city,
				// 'zip' => $this->zip,
				// 'country' => $this->country,
				// 'tax_id' => $this->tax_id,
				'created_at' => $this->created_at,
				'updated_at' => $this->updated_at
			]
		];
    }
}
