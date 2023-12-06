<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JiraProjectLinkResource extends JsonResource
{
	/**
	 * Transform JiraProjectLink into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		return [
			"site" => [
				"id" => $this->site_id,
				"name" => $this->site_name,
				"url" => $this->site_url,
			],
			"project" => [
				"id" => $this->jira_project_id,
				"name" => $this->jira_project_name,
				"key" => $this->jira_project_key,
				"issuetype" => $this->jira_issuetype_id
			],
			"options" => [
				"sync_bugs_to_jira" => $this->sync_bugs_to_jira,
				"sync_bugs_from_jira" => $this->sync_bugs_from_jira,
				"sync_comments_to_jira" => $this->sync_comments_to_jira,
				"sync_comments_from_jira" => $this->sync_comments_from_jira,
				"update_status_to_jira" => $this->update_status_to_jira,
				"update_status_from_jira" => $this->update_status_from_jira
			]

		];
	}
}
