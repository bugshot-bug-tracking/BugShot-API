<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use App\Events\CompanyMembersUpdated;
use App\Events\InvitationDeleted;
use App\Events\OrganizationMembersUpdated;
use App\Events\ProjectMembersUpdated;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\OrganizationUserRoleResource;
use App\Http\Resources\CompanyUserRoleResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\InvitationStatusResource;
use App\Http\Resources\ProjectUserRoleResource;

// Models
use App\Models\Organization;
use App\Models\OrganizationUserRole;
use App\Models\Company;
use App\Models\CompanyUserRole;
use App\Models\Invitation;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectUserRole;
use Illuminate\Notifications\DatabaseNotification;

// Services
use App\Services\NotificationService;

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
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
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
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
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
	 * @param  Invitation  $invitation
	 * @param  NotificationService $notificationService
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/invitations/{invitation_id}",
	 *	tags={"Invitation"},
	 *	summary="Delete a invitation.",
	 *	operationId="deleteInvitation",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
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
	public function destroy(Invitation $invitation, NotificationService $notificationService)
	{
		// Check if the user is authorized to delete the invitation
		$this->authorize('delete', $invitation);

		$val = $invitation->delete();

		// Delete the notification
		$notification = DatabaseNotification::where("data", "like", "%" . $invitation->id . "%")->where("type", "App\Notifications\InvitationReceivedNotification")->first();
		$user = User::where("email", $invitation->target_email)->first();
		$notification ? $notificationService->delete($user, $notification) : true;

		return response($val, 204);
	}

	/**
	 * Update the resource to a new status.
	 *
	 * @param  Invitation  $invitation
	 * @param  NotificationService $notificationService
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
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
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
	public function accept(User $user, Invitation $invitation, NotificationService $notificationService)
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
			case 'organization':
				return $this->acceptOrganization($user, $invitation, $invitable);
				break;

			case 'company':
				return $this->acceptCompany($user, $invitation, $invitable);
				break;

			case 'project':
				return $this->acceptProject($user, $invitation, $invitable);
				break;
		}

		// Delete the notification
		$notification = DatabaseNotification::where("data", "like", "%" . $invitation->id . "%")->where("type", "App\Notifications\InvitationReceivedNotification")->first();
		$notification ? $notificationService->delete($user, $notification) : true;

		return response()->json([
			"errors" => [
				"status" => 422,
			]
		], 422);
	}

	/**
	 * Update the resource to a new status.
	 *
	 * @param  Invitation  $invitation
	 * @param  NotificationService  $notificationService
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
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
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
	public function decline(User $user, Invitation $invitation, NotificationService $notificationService)
	{
		// Check if the user is authorized to decline the invitation
		$this->authorize('decline', $invitation);

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

		$invitation->update(["status_id" => 3]);
		broadcast(new InvitationDeleted($invitation, $invitation->invitable_type))->toOthers();

		// Delete the notification
		$notification = DatabaseNotification::where("data", "like", "%" . $invitation->id . "%")->where("type", "App\Notifications\InvitationReceivedNotification")->first();
		$notification ? $notificationService->delete($user, $notification) : true;

		return response()->json("", 204);
	}

	/**
	 * Generate the link between user, organization and role.
	 *
	 * @param  \App\Models\User  $user
	 * @param  \App\Models\Invitation  $invitation
	 * @param  \App\Models\Organization  $organization
	 * @return \App\Http\Resources\OrganizationUserRoleResource
	 */
	private function acceptOrganization(User $user, Invitation $invitation, Organization $organization)
	{
		// Check if the user is already part of this organization
		if ($user->organizations->find($organization) !== NULL) {
			$invitation->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => __('application.already-part-of-the-organization')
			]], 288);
		}

		$this->attachUserToOrganization($organization, $user, $invitation->role_id);

		$invitation->update(["status_id" => 2]);

		broadcast(new OrganizationMembersUpdated($organization))->toOthers();
		broadcast(new InvitationDeleted($invitation))->toOthers();

		return new OrganizationUserRoleResource(OrganizationUserRole::where('organization_id', $organization->id)->first());
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

		// Check if the user is already part of this organization
		if ($user->organizations->find($company->organization) == NULL) {
			if($invitation->role_id < 3)			
				$this->attachUserToOrganization($company->organization, $user, 2); // Team
			else
				$this->attachUserToOrganization($company->organization, $user, 3); // Client
		}

		$invitation->update(["status_id" => 2]);

		broadcast(new CompanyMembersUpdated($company))->toOthers();
		broadcast(new InvitationDeleted($invitation))->toOthers();

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
			if($invitation->role_id < 3)
				$user->companies()->attach($project->company->id, ['role_id' => 2]); // Team
			else
				$user->companies()->attach($project->company->id, ['role_id' => 3]); // Client
		}

		// Check if the user is already part of this organization
		if ($user->organizations->find($project->company->organization) == NULL) {
			if($invitation->role_id < 3)
				$this->attachUserToOrganization($project->company->organization, $user, 2); // Team
			else
				$this->attachUserToOrganization($project->company->organization, $user, 3); // Client
		}

		$invitation->update(["status_id" => 2]);

		broadcast(new ProjectMembersUpdated($project))->toOthers();
		broadcast(new InvitationDeleted($invitation))->toOthers();

		return new ProjectUserRoleResource(ProjectUserRole::where('project_id', $project->id)->first());
	}


	// Attaches the user to the organization while also adding existing subs
	private function attachUserToOrganization($organization, $user, $role) {
		$organizationUserRole = OrganizationUserRole::where("user_id", $user->id)->whereNot("subscription_item_id", NULL)->first();

		if($organizationUserRole != NULL) {
			$user->organizations()->attach($organization->id, ['role_id' => $role, "subscription_item_id" => $organizationUserRole->subscription_item_id]); // Adding the subscription is only for the current state. Later, when subscriptions should be restricted, we need to change that
		} else {
			$user->organizations()->attach($organization->id, ['role_id' => $role]); // Adding the subscription is only for the current state. Later, when subscriptions should be restricted, we need to change that
		}

	}
}
