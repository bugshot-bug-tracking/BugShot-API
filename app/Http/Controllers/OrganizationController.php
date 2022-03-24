<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\OrganizationUserRoleResource;

// Services
use App\Services\InvitationService;

// Models
use App\Models\Organization;
use App\Models\User;
use App\Models\OrganizationUserRole;

// Requests
use App\Http\Requests\OrganizationRequest;
use App\Http\Requests\InvitationRequest;

/**
 * @OA\Tag(
 *     name="Organization",
 * )
 */
class OrganizationController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Get(
	 *	path="/organizations",
	 *	tags={"Organization"},
	 *	summary="All organizations.",
	 *	operationId="allOrganizations",
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
	 * 	@OA\Parameter(
	 *		name="timestamp",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Organization")
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
	 *)
	 *
	 **/
	public function index(Request $request)
	{
		// Get timestamp
		$timestamp = $request->header('timestamp');

		// Check if the request includes a timestamp and query the organizations accordingly
        if($timestamp == NULL) {
            $organizations = $this->user->organizations->sortBy('designation');
        } else {
            $organizations = $this->user->organizations
				->where("organizations.updated_at", ">", date("Y-m-d H:i:s", $timestamp))
				->sortBy('designation');
        }

		return OrganizationResource::collection($organizations);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\OrganizationRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Post(
	 *	path="/organizations",
	 *	tags={"Organization"},
	 *	summary="Store one organization.",
	 *	operationId="storeOrganization",
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
	 *
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The organization name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              required={"designation"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Organization"
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
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function store(OrganizationRequest $request)
	{	
		// Check if the the request already contains a UUID for the organization
		$id = $this->setId($request);

		// Store the new organization in the database
        $organization = Organization::create([
			"id" => $id,
			"user_id" => $this->user->id,
			"designation" => $request->designation
		]);

		// Add the organization_id to the user
		$organization->users()->attach($this->user->id, ['role_id' => 1]);

		return new OrganizationResource($organization);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Get(
	 *	path="/organizations/{organization_id}",
	 *	tags={"Organization"},
	 *	summary="Show one organization.",
	 *	operationId="showOrganization",
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
	 *
	 *	@OA\Parameter(
	 *		name="organization_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Organization"
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
	public function show(Organization $organization)
	{
		// Check if the user is authorized to view the organization
		$this->authorize('view', $organization);

		return new OrganizationResource($organization);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\OrganizationRequest  $request
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Put(
	 *	path="/organizations/{organization_id}",
	 *	tags={"Organization"},
	 *	summary="Update a organization.",
	 *	operationId="updateOrganization",
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
	 *	@OA\Parameter(
	 *		name="organization_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="_method",
	 *		required=true,
	 *		in="query",
	 *		@OA\Schema(
	 *			type="string",
	 *			default="PUT"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The organization name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              required={"designation"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Organization"
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
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function update(OrganizationRequest $request, Organization $organization)
	{
		// Check if the user is authorized to update the organization
		$this->authorize('update', $organization);

		// Update the organization
		$organization->update([
            'designation' => $request->designation
        ]);
		
		return new OrganizationResource($organization);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/organizations/{organization_id}",
	 *	tags={"Organization"},
	 *	summary="Delete a organization.",
	 *	operationId="deleteOrganization",
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
	 *	@OA\Parameter(
	 *		name="organization_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
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
	public function destroy(Organization $organization)
	{
		// Check if the user is authorized to delete the organization
		$this->authorize('delete', $organization);

		$val = $organization->delete();

		return response($val, 204);
	}

	/**
	 * Display a list of users that belongs to the organization.
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Get(
	 *	path="/organizations/{organization_id}/users",
	 *	tags={"Organization"},
	 *	summary="All organization users.",
	 *	operationId="allOrganizationsUsers",
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
	 *
	 *	@OA\Parameter(
	 *		name="organization_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/OrganizationUserRole")
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
	 *)
	 *
	 **/
	public function users(Organization $organization)
	{
		// Check if the user is authorized to view the users of the organization
		$this->authorize('viewUsers', $organization);

		return OrganizationUserRoleResource::collection(
			OrganizationUserRole::where("organization_id", $organization->id)
				->with('organization')
				->with('user')
				->with("role")
				->get()
		);
	}

	/**
	 * Remove a user from the organization
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/organizations/{organization_id}/users/{user_id}",
	 *	tags={"Organization"},
	 *	summary="Remove user from the organization.",
	 *	operationId="removeOrganizationUser",
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
	 *	@OA\Parameter(
	 *		name="organization_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
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
	 *)
	 *
	 **/
	public function removeUser(Organization $organization, User $user)
	{
		// Check if the user is authorized to view the users of the organization
		$this->authorize('removeUser', $organization);

		$val = $organization->users()->detach($user);
	
		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/organizations/{organization_id}/invitations",
	 *	tags={"Organization"},
	 *	summary="All organization invitations.",
	 *	operationId="allOrganizationsInvitations",
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
	 *
	 *	@OA\Parameter(
	 *		name="organization_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
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
	 *)
	 *
	 **/
	public function invitations(Organization $organization)
	{
		// Check if the user is authorized to view the invitations of the organization
		$this->authorize('viewInvitations', $organization);
		
		return InvitationResource::collection($organization->invitations);
	}

	/**
	 * @OA\Post(
	 *	path="/organizations/{organization_id}/invite",
	 *	tags={"Organization"},
	 *	summary="Invite a user to the organization and asign a role to him",
	 *	operationId="inviteOrganization",
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
	 *	@OA\Parameter(
	 *		name="organization_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The invited user email.",
	 *                  property="target_email",
	 *					type="string"
	 *              ),
	 *              @OA\Property(
	 *                  description="The invited user role.",
	 *                  property="role_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *              required={"target_email","role_id"}
	 *          )
	 *      )
	 *  ),
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
	 *	@OA\Response(
	 *		response=409,
	 *		description="The request could not be completed due to a conflict with the current state of the resource."
	 *	),
	 *	@OA\Response(
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function invite(InvitationRequest $request, Organization $organization, InvitationService $invitationService)
	{
		// Check if the user is authorized to invite users to the organization
		$this->authorize('invite', $organization);

		// Check if the user has already been invited to the organization or is already part of it
        $recipient_mail = $request->target_email;
		$recipient = User::where('email', $recipient_mail)->first();
		if($organization->invitations->contains('target_email', $recipient_mail) || $organization->users->contains($recipient)) {
			return response()->json(["data" => [
				"message" => __('application.organization-user-already-invited')
			]], 409);
		}

		$id = $this->setId($request);
		$invitation = $invitationService->send($request, $organization, $id, $recipient_mail);

		return new InvitationResource($invitation);
	}
}
