<?php

namespace App\Http\Resources;

use App\Models\Status;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

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
		$bug = array(
			"id" => $this->id,
			"type" => "Bug",
			"attributes" => [
				"project_id" => $this->project_id,
				"creator" => new UserResource(User::find($this->user_id)),
				"designation" => $this->designation,
				"description" => $this->description,
				"url" => $this->url,
				"time_estimation" => $this->time_estimation,
				"approval_status" => array(
					"id" => $this->approval_status_id,
					"designation" => $this->approvalStatus ? $this->approvalStatus->designation : NULL
				),
				"status_id" => $this->status_id,
				"order_number" => $this->order_number,
				"ai_id" => $this->ai_id,
				"client_id" => $this->client_id,
				"priority" => new PriorityResource($this->priority),
				"operating_system" => $this->operating_system,
				"browser" => $this->browser,
				"selector" => $this->selector,
				"resolution" => $this->resolution,
				"deadline" => $this->deadline,
				"done_at" => $this->done_at,
				"archived_at" => $this->archived_at,
				"created_at" => $this->created_at,
				"updated_at" => $this->updated_at,
				"deleted_at" => $this->deleted_at
			]
		);

		$header = $request->header();

		// Check if the response should contain the respective attachments
		if(array_key_exists('include-attachments', $header) && $header['include-attachments'][0] == "true") {
			$attachments = $this->attachments;
			$bug['attributes']['attachments'] = AttachmentResource::collection($attachments);
		}

		// Check if the response should contain the respective screenshots
		if(array_key_exists('include-screenshots', $header) && $header['include-screenshots'][0] == "true") {
			$screenshots = $this->screenshots;
			$bug['attributes']['screenshots'] = ArchivedScreenshotResource::collection($screenshots);
		}

		// Check if the response should contain the respective comments
		if(array_key_exists('include-comments', $header) && $header['include-comments'][0] == "true") {
			$comments = $this->comments;
			$bug['attributes']['comments'] = CommentResource::collection($comments);
		}

		// Check if the response should contain the respective bug users
		if(array_key_exists('include-bug-users', $header) && $header['include-bug-users'][0] == "true") {
			$users = $this->users;
			$bug['attributes']['users'] = UserResource::collection($users);
		}

		// Check if the response should contain status infos (interfaces)
		if(array_key_exists('include-status-info', $header) && $header['include-status-info'][0] == "true") {
			$bug['attributes']['status_designation'] = Status::find($this->status_id)->designation;
			$bug['attributes']['status_order_number'] = Status::find($this->status_id)->order_number;
		}

		return $bug;
	}
}
