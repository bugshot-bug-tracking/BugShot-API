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
        $project->update($request->all());
        $project->update([
            "url" => substr($request->url, -1) == '/' ? substr($request->url, 0, -1) : $request->url
        ]);

        $resource = new ProjectResource($project);
		TriggerInterfacesJob::dispatch($apiCallService, $resource, "project-updated-info", $project->id, $request->get('session_id'));
        broadcast(new ProjectUpdated($project))->toOthers();

		return $resource;
    }

    public function users(Project $project, $withOwner = false)
	{
        if($withOwner){
            $returnCollections = UserResource::collection($project->users);
			$returnCollections = $returnCollections->push(new UserResource($project->creator));
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
}
