<?php

namespace App\Services;

//Controller

use App\Events\InvitationCreated;
use App\Events\ProjectUpdated;
use App\Http\Controllers\ProjectController;

//Requests
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\ProjectUpdateRequest;

//Resources
use App\Http\Resources\InvitationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUserRoleResource;
use App\Http\Resources\UserResource;
use App\Jobs\TriggerInterfacesJob;
use App\Models\Client;
//Models
use App\Models\Company;
use App\Models\Project;
use App\Models\ProjectUserRole;
use App\Models\User;

class ProjectService
{

    public function update(ProjectUpdateRequest $request, Company $company, Project $project, ImageService $imageService, ApiCallService $apiCallService)
    {
        // Check if the project comes with an image (or a color)
        $image = $project->image;

		if($request->has("base64")){
			if($request->base64 != NULL){
				$image = $imageService->store($request->base64, $image);
				$image != false ? $project->image()->save($image) : true;
			} else{
				$imageService->delete($image);
			}
		}

        // Update the project
        $project->fill($request->all());
        $project->fill([
            "url" => substr($request->url, -1) == '/' ? substr($request->url, 0, -1) : $request->url
        ]);

		// Do the save and fire the custom event
		$project->fireCustomEvent('projectUpdated');
		$project->save();

        $resource = new ProjectResource($project);
		TriggerInterfacesJob::dispatch($apiCallService, $resource, "project-updated-info", $project->id, $request->get('session_id'));
        broadcast(new ProjectUpdated($project))->toOthers();

		return $resource;
    }

    public function users(Project $project, $withOwner = false)
	{
        if($withOwner){
            $returnCollections["projectUsers"] = UserResource::collection($project->users);
			// $returnCollections = $returnCollections->push(new UserResource($project->creator)); // Not required anymore as the project creator should be part of the project users anyway (ProjectUserRole table)

            return $returnCollections;
        }

		return ProjectUserRoleResource::collection(
			ProjectUserRole::where("project_id", $project->id)->get()
		);
	}

    public function invite(InvitationRequest $request, Project $project, InvitationService $invitationService, ProjectController $projectController)
	{
		// Check if the user has already been invited to the project or is already part of it
		$recipient_mail = $request->target_email;
		$recipient = User::where('email', $recipient_mail)->first();

        if(!$project->invitations->where('target_email', $recipient_mail)->where('status_id', 1)->isEmpty() || $project->users->contains($recipient)) {
			return response()->json(["data" => [
				"message" => __('application.project-user-already-invited')
			]], 409);
		}

		$id = $projectController->setId($request);
		$invitation = $invitationService->send($request, $project, $id, $recipient_mail);

        broadcast(new InvitationCreated($invitation))->toOthers();

		return new InvitationResource($invitation);
	}

	/**
	 * Checks a given URL against project URLs and determine if there is a match between them.
	 *
	 * @param  Project $project  The project object.
	 * @param  string  $url   The URL to be checked.
	 *
	 * @return mixed Project and match status if matched (1: exact match, 2: wildcard match). NULL otherwise.
	 */
	public function checkUrlAgainstProject(Project $project, $url) {
		// Merge the project URL and its associated URLs into a single array
		$projectUrls = [$project->url, ...$project->urls->pluck('url')->toArray()];

		// Check if the requested URL matches the project URL origin or a wildcard URL pattern
		foreach ($projectUrls as $projectUrl) {
			// Check if the URL contains a wildcard character *
			if(str_contains($projectUrl, "*")) {
            	// If the URL matches the wildcard URL pattern or its origin, return the project and a match status of 2
				if($this->matchWildcardUrl($url, $projectUrl) || $this->matchWildcardUrlOrigin($url, $projectUrl))
					return [$project, 2];
			}
			else{
           		// If the URL is an exact match, return the project and a match status of 1
				if(rtrim($url, '/') === rtrim($projectUrl, '/')) {
					return [$project, 1];
				}
				else {
        	        // If the URL origin matches the project origin URL, return the project and a match status of 2
					if($this->checkUrlOrigin($url, $projectUrl))
						return [$project, 2];
				}
			}
		}

		return NULL;
	}

	/**
	 * Check if two URLs have the same origin (scheme and host)
	 *
	 * @param string $url1 The first URL
	 * @param string $url2 The second URL
	 * @return bool True if both URLs have the same origin, otherwise false
	 */	private function checkUrlOrigin($url1, $url2)
	{
		// Check if the URLs are not empty or if they contain wildcard characters
		if (!$url1 || !$url2) {
			return false; // Return false if any of the conditions is true
		}

		// Parse the URLs
		$parsedUrl1 = parse_url($url1);
		$parsedUrl2 = parse_url($url2);

		// Check if both URLs have been parsed successfully and if both URLs have the same scheme and host
		return $parsedUrl1 && $parsedUrl2 && $parsedUrl1['scheme'] == $parsedUrl2['scheme'] && $parsedUrl1['host'] == $parsedUrl2['host'];
	}

	// Match a URL against a wildcard URL pattern
	private function matchWildcardUrl($url, $pattern)
	{
		// Replace * with a regular expression pattern that matches any characters
		$pattern = str_replace('\*', '.*', preg_quote(rtrim($pattern, '/'), '/'));
		// Use regular expression string matching to determine if the URL matches the pattern
		return preg_match('/^' . $pattern . '\/*$/', $url);
	}

	// Match a URL against a wildcard URL pattern
	private function matchWildcardUrlOrigin($url, $pattern)
	{
		// Check if the URLs are not empty or if they contain wildcard characters
		if (!$url || !$pattern) {
			return false; // Return false if any of the conditions is true
		}

		$url_with_protocol = '';
		if(str_starts_with($pattern, "*")){
			$url_with_protocol = str_replace("*://", "http://", $pattern);
		}
		else{
			$url_with_protocol = $pattern;
		}

		// Parse the URLs
		$parsedUrl1 = parse_url($url);
		$parsedUrl2 = parse_url($url_with_protocol);

		// Check if both URLs have been parsed successfully and if both URLs have the same scheme and host
		return $parsedUrl1 && $parsedUrl2 && $this->matchWildcardUrl($parsedUrl1['host'], $parsedUrl2["host"]);
	}
}
