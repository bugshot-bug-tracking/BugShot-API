<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\OrganizationUserRoleResource;

// Services
use App\Services\InvitationService;
use App\Services\OrganizationService;

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
	 * 
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
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
        $organizations = Organization::all();

		return OrganizationResource::collection($organizations);
	}

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

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\OrganizationRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(OrganizationRequest $request, OrganizationService $organizationService, InvitationService $invitationService)
	{	
		// Check if the the request already contains a UUID for the organization
		$id = $this->setId($request);

		// Store the new organization in the database
		$organization = $organizationService->store($request, $this->user, $id);

        // Send invites to the selected recipients
		$recipients = $request->recipients;
		if($recipients != NULL) {
			foreach($recipients as $recipient) {
                $invitationService->send($request, $organization, $id, $recipient);
			}
		}

		return new OrganizationResource($organization);
	}
    // TODO: Rest of the functions down below
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
	 * 	@OA\Parameter(
	 *		name="include-projects",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-statuses",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachments",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-comments",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-organization-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-project-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-organization-image",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-project-image",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachment-base64",
	 *		required=false,
	 *		in="header"
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
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
	public function show(Organization $organization)
	{
		// Check if the user is authorized to view the organization
		$this->authorize('view', $organization);

		return new OrganizationResource($organization);
	}

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
	 *  			@OA\Property(
	 *                  description="The hexcode of the color (optional)",
	 *                  property="color_hex",
	 * 					type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The base64 string of the image belonging to the organization (optional)",
	 *                  property="base64",
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
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\OrganizationRequest  $request
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
	public function update(OrganizationRequest $request, Organization $organization, ImageService $imageService)
	{
		// Check if the user is authorized to update the organization
		$this->authorize('update', $organization);

		// Check if the organization comes with an image (or a color)
		$image = $organization->image;

		if($request->base64 != NULL && $request->base64 != 'true') {
			$image = $imageService->store($request->base64, $image);
			$image != false ? $organization->image()->save($image) : true;
			$color_hex = $organization->color_hex == $request->color_hex ? $organization->color_hex : $request->color_hex;
		} else {
			$imageService->delete($image);
			$color_hex = $request->color_hex;
		}

		// Apply default color if color_hex is null
		$color_hex = $color_hex == NULL ? '#7A2EE6' : $color_hex;

		// Update the organization
		$organization->update([
            'designation' => $request->designation,
			'color_hex' => $color_hex
        ]);
		
		return new OrganizationResource($organization);
	}

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
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Organization $organization, ImageService $imageService)
	{
		// Check if the user is authorized to delete the organization
		$this->authorize('delete', $organization);

		$val = $organization->delete();
		
		// Delete the respective image if present
		$imageService->delete($organization->image);

		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/organizations/{organization_id}/image",
	 *	tags={"Organization"},
	 *	summary="Organization image.",
	 *	operationId="showOrganizationImage",
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
	 *			@OA\Items(ref="#/components/schemas/Image")
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
	/**
	 * Display the image that belongs to the organization.
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
	public function image(Organization $organization, ImageService $imageService)
	{
		// Check if the user is authorized to view the image of the organization
		$this->authorize('viewImage', $organization);

		return new ImageResource($organization->image);
	}

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
	/**
	 * Display a list of users that belongs to the organization.
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
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
	/**
	 * Remove a user from the organization
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
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
	/**
	 * Display a list of invitations that belongs to the organization.
	 *
	 * @param  \App\Models\Organization  $organization
	 * @return \Illuminate\Http\Response
	 */
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
		$recipient = User::where('email', $request->target_email)->first();
		if($organization->invitations->contains('target_email', $request->target_email) || $organization->users->contains($recipient)) {
			return response()->json(["data" => [
				"message" => __('application.organization-user-already-invited')
			]], 409);
		}

		$id = $this->setId($request);

		$invitation = $invitationService->send($request, $organization, $id);

		return new InvitationResource($invitation);
	}
}
