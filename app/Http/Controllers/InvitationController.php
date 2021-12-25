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
use App\Models\InvitationStatus;
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
	 *	path="/user/invitations",
	 *	tags={"Invitation"},
	 *	summary="Show all invitations that the user has received.",
	 *	operationId="showInvitations",
	 *	security={ {"sanctum": {} }},
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
	public function index()
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
	 * @OA\Get(
	 *	path="/user/invitations/{invitation_id}",
	 *	tags={"Invitation"},
	 *	summary="Show one invitation.",
	 *	operationId="showInvitation",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \App\Http\Resources\InvitationResource
	 */
	public function show(Invitation $invitation)
	{
		// Check if the user is authorized to view the invitation
		$this->authorize('view', $invitation);

		return new InvitationResource($invitation);
	}

	/**
	 * @OA\Delete(
	 *	path="/user/invitations/{invitation_id}",
	 *	tags={"Invitation"},
	 *	summary="Delete a invitation.",
	 *	operationId="deleteInvitation",
	 *	security={ {"sanctum": {} }},

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
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Invitation $invitation)
	{
		// Check if the user is authorized to delete the invitation
		$this->authorize('delete', $invitation);

		$val = $invitation->update([
			"deleted_at" => new \DateTime()
		]);

		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/user/invitations/{invitation_id}/accept",
	 *	tags={"Invitation"},
	 *	summary="Accept one invitation.",
	 *	operationId="acceptInvitation",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Update the resource to a new status.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \Illuminate\Http\Response
	 */
	public function accept(Invitation $invitation)
	{
		// Check if the user is authorized to accept the invitation
		$this->authorize('accept', $invitation);

		if (Auth::user()->email !== $invitation->target_email)
			return response()->json([
				"errors" => [
					"status" => 403,
					"details" => "This invitation is not for you!"
				]
			], 403);

		if ($invitation->status_id !== 1)
			return response()->json(["data" => [
				"message" => "Invitation already processed."
			]], 288);

		$invitable = $invitation->invitable;

		switch ($invitation->invitable_type) {
			case Company::class:
				return $this->acceptCompany($invitation, $invitable);
				break;

			case Project::class:
				return $this->acceptProject($invitation, $invitable);
				break;
		}

		return response()->json([
			"errors" => [
				"status" => 422,
			]
		], 422);
	}

	/**
	 * @OA\Get(
	 *	path="/user/invitations/{invitation_id}/decline",
	 *	tags={"Invitation"},
	 *	summary="Decline one invitation.",
	 *	operationId="declineInvitation",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Update the resource to a new status.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \Illuminate\Http\Response
	 */
	public function decline(Invitation $invitation)
	{
		// Check if the user is authorized to decline the invitation
		$this->authorize('decline', $invitation);

		if (Auth::id() !== $invitation->target_email)
			return response()->json([
				"errors" => [
					"status" => 403,
					"details" => "This invitation is not for you!"
				]
			], 403);

		if ($invitation->status_id !== 1)
			return response()->json(["data" => [
				"message" => "Invitation already processed."
			]], 288);

		$invitation->update(["status_id" => 3]);
		return response()->json("", 204);
	}

	/**
	 * Generate the link between user, company and role.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @param  \App\Models\Company  $company
	 * @return \App\Http\Resources\CompanyUserRoleResource
	 */
	private function acceptCompany(Invitation $invitation, Company $company)
	{
		$user = Auth::user();

		// Check if the user is already part of this company
		if ($user->companies->find($company) !== NULL) {
			$invitation->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => "You are already part of the company."
			]], 288);
		}

		$user->companies()->attach($company->id, ['role_id' => $invitation->role_id]);
		$invitation->update(["status_id" => 2]);

		return new CompanyUserRoleResource(CompanyUserRole::where('company_id', $company->id)->first());
	}

	/**
	 * Generate the link between user, project and role.
	 * And if needed between user, company and role.
	 * @param  \App\Models\Invitation  $invitation
	 * @param  \App\Models\Project  $project
	 * @return \App\Http\Resources\ProjectUserRoleResource
	 */
	private function acceptProject(Invitation $invitation, Project $project)
	{
		$user = Auth::user();

		// Check if the user is already part of this project
		if ($user->projects->find($project) !== NULL) {
			$invitation->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => "You are already part of the project."
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
