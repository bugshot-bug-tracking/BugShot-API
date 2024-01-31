<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class PostJiraBugResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * This is the form the Bug data from our side is sent to Jira
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{

		$content = [
			$this->resource->description
		];

		array_push($content, "#Priority\n{$this->resource->priority->designation}");

		if ($this->resource->deadline) {
			array_push($content, "#Deadline\n{$this->resource->deadline}");
		}
		if ($this->resource->url) {
			array_push($content, "#URL\n{$this->resource->url}");
		}
		if ($this->resource->time_estimation) {

			$type = "minute";

			switch ($this->resource->time_estimation_type) {
				case "m":
					$type = "minute";
					break;
				case "h":
					$type = "hour";
					break;
				case "d":
					$type = "day";
					break;
				case "w":
					$type = "week";
					break;
			}

			array_push($content, "#Time estimate\n{$this->resource->time_estimation} {$type}");
		}

		if (
			$this->resource->resolution ||
			$this->resource->selector ||
			$this->resource->browser ||
			$this->resource->operating_system
		) {
			array_push($content, "#Technical data\n{$this->resource->time_estimation}");

			if ($this->resource->resolution) {
				array_push($content, "##Resolution\n{$this->resource->resolution}");
			}
			if ($this->resource->selector) {
				array_push($content, "##Selector\n{$this->resource->selector}");
			}
			if ($this->resource->browser) {
				array_push($content, "##Browser\n{$this->resource->browser}");
			}
			if ($this->resource->operating_system) {
				array_push($content, "##Operating System\n{$this->resource->operating_system}");
			}
		}

		if ($this->creator) {
			array_push($content, "#Creator\n{$this->creator->first_name} {$this->creator->last_name}");
		} else if ($this->guestCreator) {
			array_push($content, "#Creator\n" . ($this->guestCreator->name ? $this->guestCreator->name : $this->guestCreator->email));
		} else {
			array_push($content, "#Creator\nAnonymous");
		}

		return [
			"fields" => [
				// this is static because 10004 represents bugs, in the future if BugShot will support multiple report types this can be changed
				"issuetype" => ["id" => $this->resource->project->jiraLink->jira_issuetype_id],
				// in BugShot "Critical" has id 4 but in Jira "Critical/Highest" is 1 and Jira "Lowest" (id 5) is ignored because there is no equivalent
				// "priority" => ["id" => (string)(4 - $this->resource->priority_id + 1)],
				"project" => ["id" => $this->resource->project->jiraLink->jira_project_id],
				"summary" => $this->resource->designation,
				"description" => join("\n", $content)
			],
		];
	}
}
