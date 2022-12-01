<?php

namespace App\Services;

//Controller
use App\Http\Controllers\ProjectController;

//Requests
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\ProjectUpdateRequest;

//Resources
use App\Http\Resources\InvitationResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUserRoleResource;
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
        if ($request->base64 != NULL && $request->base64 != 'true') {
            $image = $imageService->store($request->base64, $image);
            $image != false ? $project->image()->save($image) : true;
            //ev Fehler? Proj statt comp?
            $color_hex = $company->color_hex == $request->color_hex ? $company->color_hex : $request->color_hex;
        } else {
            $imageService->delete($image);
            $color_hex = $request->color_hex;
        }

        // Apply default color if color_hex is null
        $color_hex = $color_hex == NULL ? '#7A2EE6' : $color_hex;

        // Update the project
        $project->update($request->all());
        $project->update([
            "company_id" => $company->id,
            "color_hex" => $color_hex,
            "url" => substr($request->url, -1) == '/' ? substr($request->url, 0, -1) : $request->url // Check if the given url has "/" as last char and if so, store url without it
        ]);

        return $apiCallService->triggerInterfaces(new ProjectResource($project), 6, $project->id);
    }

    public function users(Project $project)
	{
		return ProjectUserRoleResource::collection(
			ProjectUserRole::where("project_id", $project->id)->get()
		);
	}

    public function invite(InvitationRequest $request, Project $project, InvitationService $invitationService, ProjectController $projectController)
	{
		// Check if the user has already been invited to the project or is already part of it
		$recipient_mail = $request->target_email;
		$recipient = User::where('email', $recipient_mail)->first();
        if(!isset($recipient)) {
			return response()->json(["data" => [
				"message" => __('application.project-user-not-found')
			]], 409);
		}else if(!$project->invitations->where('target_email', $recipient_mail)->where('status_id', 1)->isEmpty() || $project->users->contains($recipient)) {
			return response()->json(["data" => [
				"message" => __('application.project-user-already-invited')
			]], 409);
		}

		$id = $projectController->setId($request);
		$invitation = $invitationService->send($request, $project, $id, $recipient_mail);

		return new InvitationResource($invitation);
	}
}
