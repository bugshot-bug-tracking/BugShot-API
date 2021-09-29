<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$user = $this->user;

		return [
			"id" => $this->id,
			"type" => "Comment",
			"attributes" => [
				"bug_id" => $this->bug_id,
				"user" => [
					"id" => $user->id,
					"first_name" => $user->first_name,
					"last_name" => $user->last_name,
				],
				"content" => $this->content,
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at,
			]
		];
	}
}
