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
	 *	path="/invitations/{id}",
	 *	tags={"Invitation"},
	 *	summary="Show one invitation.",
	 *	operationId="showInvitation",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
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
		return new InvitationResource($invitation);
	}

	/**
	 * @OA\Delete(
	 *	path="/invitations/{id}",
	 *	tags={"Invitation"},
	 *	summary="Delete a invitation.",
	 *	operationId="deleteInvitation",
	 *	security={ {"sanctum": {} }},

	 *	@OA\Parameter(
	 *		name="id",
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
		$val = $invitation->update([
			"deleted_at" => new \DateTime()
		]);

		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/invitations/statuses",
	 *	tags={"Invitation"},
	 *	summary="Show all invitation possible statuses.",
	 *	operationId="showInvitationStatuses",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/InvitationStatus")
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
	public function statusIndex()
	{
		return InvitationStatusResource::collection(InvitationStatus::all());
	}

	/**
	 * @OA\Get(
	 *	path="/invitations/statuses/{id}",
	 *	tags={"Invitation"},
	 *	summary="Show invitation status.",
	 *	operationId="showInvitationStatus",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/InvitationStatus/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/InvitationStatus"
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
	public function statusShow(int $invitationStatus_id)
	{
		return new InvitationResource(InvitationStatus::find($invitationStatus_id));
	}

	/**
	 * @OA\Post(
	 *	path="/invitations/{id}/accept",
	 *	tags={"Invitation"},
	 *	summary="Accept one invitation.",
	 *	operationId="acceptInvitation",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
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
		if (Auth::id() !== $invitation->target_id)
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
	 * @OA\Post(
	 *	path="/invitations/{id}/decline",
	 *	tags={"Invitation"},
	 *	summary="Decline one invitation.",
	 *	operationId="declineInvitation",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
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
		if (Auth::id() !== $invitation->target_id)
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
		// if a previous invitation was accepted
		$cur = CompanyUserRole::where([
			["company_id", $company->id],
			["user_id", $invitation->target_id]
		])->get();

		if ($cur->count() !== 0) {
			$invitation->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => "A previous invitation was already accepted."
			]], 288);
		}

		$companyUserRole = CompanyUserRole::create([
			"company_id" => $company->id,
			"user_id" => $invitation->target_id,
			"role_id" => $invitation->role_id
		]);

		$invitation->update(["status_id" => 2]);

		return new CompanyUserRoleResource($companyUserRole);
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
		// if a previous invitation was accepted
		$pur = ProjectUserRole::where([
			["project_id", $project->id],
			["user_id", $invitation->target_id]
		])->get();

		if ($pur->count() !== 0) {
			$invitation->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => "A previous invitation was already accepted."
			]], 288);
		}

		$projectUserRole = ProjectUserRole::create([
			"project_id" => $project->id,
			"user_id" => $invitation->target_id,
			"role_id" => $invitation->role_id
		]);

		// if user not in company add it to it
		$cur = CompanyUserRole::where([
			["company_id", $project->company->id],
			["user_id", $invitation->target_id]
		])->get();

		if ($cur->count() === 0)
			CompanyUserRole::create([
				"company_id" => $project->company->id,
				"user_id" => $invitation->target_id,
				"role_id" => 7
			]);

		$invitation->update(["status_id" => 2]);

		return new ProjectUserRoleResource($projectUserRole);
	}
}
