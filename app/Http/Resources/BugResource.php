<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BugResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$status = $this->status;
		$priority = $this->priority;

		// Check if the response should contain attachments, screenshots and/or comments
		$header = $request->header();
		$attachments = array_key_exists('include-attachments', $header) && $header['include-attachments'][0] == "true" ? $this->attachments : [];
		$screenshots = array_key_exists('include-screenshots', $header) && $header['include-screenshots'][0] == "true" ? $this->screenshots : [];
		$comments = array_key_exists('include-comments', $header) && $header['include-comments'][0] == "true" ? $this->comments : [];
		$users = array_key_exists('include-users', $header) && $header['include-users'][0] == "true" ? $this->users : [];
		
		return [
			"id" => $this->id,
			"type" => "Bug",
			"attributes" => [
				"project_id" => $this->project_id,
				"user_id" => $this->user_id,
				"designation" => $this->designation,
				"description" => $this->description,
				"url" => $this->url,
				"status" => [
					"id" => $status->id,
					"designation" => $status->designation,
				],
				"priority" => [
					"id" => $priority->id,
					"designation" => $priority->designation,
				],
				"operating_system" => $this->operating_system,
				"browser" => $this->browser,
				"selector" => $this->selector,
				"resolution" => $this->resolution,
				"deadline" => $this->deadline,
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at,
				"attachments" => AttachmentResource::collection($attachments),
				"screenshots" => ScreenshotResource::collection($screenshots),
				"comments" => CommentResource::collection($comments),
				"users" => UserResource::collection($users)
			]
		];
	}
}
