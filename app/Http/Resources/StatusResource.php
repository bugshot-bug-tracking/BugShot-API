<?php

namespace App\Http\Resources;

use App\Models\Priority;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		$status = array(
			"id" => $this->id,
			"type" => "Status",
			"attributes" => [
				"designation" => $this->designation,
				"order_number" => $this->order_number,
				"project_id" => $this->project_id,
				"permanent" => $this->permanent,
				"created_at" => $this->created_at,
                "updated_at" => $this->updated_at
			]
		);

		$header = $request->header();

		// Check if the response should contain the respective bugs
		if(array_key_exists('include-bugs', $header) && $header['include-bugs'][0] == "true") {
			if(array_key_exists('filter-bugs-by-assigned', $header) && $header['filter-bugs-by-assigned'][0] == "true") {
				$bugs = Auth::user()->bugs();
			} else {
				$bugs = $this->bugs();
			}

			// Add filters to the query
			$bugs = $bugs
				->when(array_key_exists('filter-bugs-by-deadline', $header) && !empty($header['filter-bugs-by-deadline'][0]), function ($query) use ($header) {
					$deadline = $header['filter-bugs-by-deadline'][0];
					$array = explode('|', $deadline);
					$operator = $array[0];
					$date = date("Y-m-d H:i:s", $array[1]);

					return $query->where("deadline", $operator, $date);
				})
				->when(array_key_exists('filter-bugs-by-creator-id', $header) && !empty($header['filter-bugs-by-creator-id'][0]), function ($query) use ($header) {
					$creatorId = $header['filter-bugs-by-creator-id'][0];

					return $query->where("user_id", $creatorId);
				})
				->when(array_key_exists('filter-bugs-by-priority', $header) && !empty($header['filter-bugs-by-priority'][0]), function ($query) use ($header) {
					$designation = $header['filter-bugs-by-priority'][0];
					$priority = Priority::where('designation', $designation)->firstOrFail();

					return $query->where("priority_id", $priority->id);
				})
				->where("bugs.archived_at", NULL)
				->get();

			$status['attributes']['bugs'] = BugResource::collection($bugs);
		}

		return $status;
	}
}
