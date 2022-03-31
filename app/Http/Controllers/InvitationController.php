<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\CompanyUserRoleResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\InvitationStatusResource;
use App\Http\Resources\ProjectUserRoleResource;

// Models
use App\Models\Company;
use App\Models\CompanyUserRole;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectUserRole;

/**
 * @OA\Tag(
 *     name="Invitation",
 * )
 */
class InvitationController extends Controller
{

	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/invitations",
	 *	tags={"Invitation"},
	 *	summary="Show all invitations that the user has received.",
	 *	operationId="showInvitations",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Invitation")
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 * )
	 **/
	public function index(User $user)
	{
		// Check if the user is authorized to list the invitations
		$this->authorize('viewAny', Invitation::class);

		$invitations = Invitation::where([
			["target_email", Auth::user()->email],
			["status_id", 1]
		])->get();

		return InvitationResource::collection($invitations);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \App\Http\Resources\InvitationResource
	 */
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/invitations/{invitation_id}",
	 *	tags={"Invitation"},
	 *	summary="Show one invitation.",
	 *	operationId="showInvitation",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 * 
	 *	@OA\Parameter(
	 *		name="invitation_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Invitation/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Invitation"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 * )
	 **/
	public function show(User $user, Invitation $invitation)
	{
		// Check if the user is authorized to view the invitation
		$this->authorize('view', $invitation);

		return new InvitationResource($invitation);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/users/{user_id}/invitations/{invitation_id}",
	 *	tags={"Invitation"},
	 *	summary="Delete a invitation.",
	 *	operationId="deleteInvitation",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="invitation_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Invitation/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=204,
	 *		description="Success",
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 * )
	 **/
	public function destroy(User $user, Invitation $invitation)
	{
		// Check if the user is authorized to delete the invitation
		$this->authorize('delete', $invitation);

		$val = $invitation->delete();

		return response($val, 204);
	}

	/**
	 * Update the resource to a new status.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/invitations/{invitation_id}/accept",
	 *	tags={"Invitation"},
	 *	summary="Accept one invitation.",
	 *	operationId="acceptInvitation",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 * 
	 *	@OA\Parameter(
	 *		name="invitation_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Invitation/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Invitation"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=288,
	 *		description="Request already processed.",
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 * )
	 **/
	public function accept(User $user, Invitation $invitation)
	{
		// Check if the user is authorized to accept the invitation
		$this->authorize('accept', $invitation);

		if (Auth::user()->email !== $invitation->target_email)
			return response()->json([
				"errors" => [
					"status" => 403,
					"details" => __('application.invitation-not-for-you')
				]
			], 403);

		if ($invitation->status_id !== 1)
			return response()->json(["data" => [
				"message" => __('application.invitation-already-in-progress')
			]], 288);

		$invitable = $invitation->invitable;

		switch ($invitation->invitable_type) {
			case Company::class:
				return $this->acceptCompany($user, $invitation, $invitable);
				break;

			case Project::class:
				return $this->acceptProject($user, $invitation, $invitable);
				break;
		}

		return response()->json([
			"errors" => [
				"status" => 422,
			]
		], 422);
	}

	/**
	 * Update the resource to a new status.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/invitations/{invitation_id}/decline",
	 *	tags={"Invitation"},
	 *	summary="Decline one invitation.",
	 *	operationId="declineInvitation",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="user_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/User/properties/id"
	 *		)
	 *	),
	 * 
	 *	@OA\Parameter(
	 *		name="invitation_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Invitation/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=204,
	 *		description="Success",
	 *	),
	 *	@OA\Response(
	 *		response=288,
	 *		description="Request already processed.",
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 * )
	 **/
	public function decline(User $user, Invitation $invitation)
	{
		// Check if the user is authorized to decline the invitation
		$this->authorize('decline', $invitation);

		if (Auth::id() !== $invitation->target_email)
			return response()->json([
				"errors" => [
					"status" => 403,
					"details" => __('application.invitation-not-for-you')
				]
			], 403);

		if ($invitation->status_id !== 1)
			return response()->json(["data" => [
				"message" => __('application.invitation-already-in-progress')
			]], 288);

		$invitation->update(["status_id" => 3]);
		return response()->json("", 204);
	}

	/**
	 * Generate the link between user, company and role.
	 *
	 * @param  \App\Models\User  $user
	 * @param  \App\Models\Invitation  $invitation
	 * @param  \App\Models\Company  $company
	 * @return \App\Http\Resources\CompanyUserRoleResource
	 */
	private function acceptCompany(User $user, Invitation $invitation, Company $company)
	{
		// Check if the user is already part of this company
		if ($user->companies->find($company) !== NULL) {
			$invitation->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => __('application.already-part-of-the-company')
			]], 288);
		}

		$user->companies()->attach($company->id, ['role_id' => $invitation->role_id]);
		$invitation->update(["status_id" => 2]);

		return new CompanyUserRoleResource(CompanyUserRole::where('company_id', $company->id)->first());
	}

	/**
	 * Generate the link between user, project and role.
	 * And if needed between user, company and role.
	 * @param  \App\Models\User  $user
	 * @param  \App\Models\Invitation  $invitation
	 * @param  \App\Models\Project  $project
	 * @return \App\Http\Resources\ProjectUserRoleResource
	 */
	private function acceptProject(User $user, Invitation $invitation, Project $project)
	{
		// Check if the user is already part of this project
		if ($user->projects->find($project) !== NULL) {
			$invitation->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => __('application.already-part-of-the-project')
			]], 288);
		}

		$user->projects()->attach($project->id, ['role_id' => $invitation->role_id]);

		// Check if the user is already part of this company
		if ($user->companies->find($project->company) == NULL) {
			$user->companies()->attach($project->company->id, ['role_id' => $invitation->role_id]);
		}

		$invitation->update(["status_id" => 2]);

		return new ProjectUserRoleResource(ProjectUserRole::where('project_id', $project->id)->first());
	}
}
