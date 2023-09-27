<?php

namespace App\Http\Resources;

use App\Models\Client;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$activities = [];
		foreach($this->resource as $activity) {
			$client = Client::find($activity->client_id);
			$activities[$client->designation] = [
				"last_active_at" => $activity->last_active_at,
				"login_counter" => $activity->login_counter
			];
		}

		return [
			"type" => "Activity",
			"attributes" => $activities
		];
	}
}
