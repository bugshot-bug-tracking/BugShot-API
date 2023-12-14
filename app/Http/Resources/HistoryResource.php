<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$message = __("actions." . $this->action->designation);
		$message = vsprintf($message, $this->args);

		return [
			"id" => $this->id,
			"type" => "History",
			"attributes" => [
				"message" => $message,
				"action" => $this->action->designation,
				"created_at" => $this->created_at,
                "updated_at" => $this->updated_at
			]
		];
	}
}
