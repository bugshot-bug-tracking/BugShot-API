<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...

use App\Events\InvitationCreated;
use App\Events\OrganizationUpdated;
use App\Events\OrganizationUserRemoved;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Stripe\StripeClient;

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
use App\Http\Requests\OrganizationStoreRequest;
use App\Http\Requests\OrganizationUpdateRequest;
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\OrganizationUserRoleUpdateRequest;
use App\Policies\OrganizationPolicy;

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
	 * @return Response
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
	 * 	@OA\Parameter(
	 *		name="timestamp",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-companies",
	 *		required=false,
	 *		in="header"
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
	 *		name="include-markers",
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
	 * 	@OA\Parameter(
	 *		name="include-organization-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-organization-users-roles",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-company-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-company-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-project-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-project-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-company-image",
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
		if ($timestamp == NULL) {
			$organizations = $this->user->organizations;
			// $createdOrganizations = $this->user->createdOrganizations;
		} else {
			$organizations = $this->user->organizations
				->where("organizations.updated_at", ">", date("Y-m-d H:i:s", $timestamp));
			// $createdOrganizations = $this->user->createdOrganizations
			// 	->where("organizations.updated_at", ">", date("Y-m-d H:i:s", $timestamp));
		}

		// Combine the two collections
		// $organizations = $organizations->concat($createdOrganizations);

		return OrganizationResource::collection($organizations->sortBy('designation'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  OrganizationStoreRequest  $request
	 * @return Response
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
	public function store(OrganizationStoreRequest $request, InvitationService $invitationService)
	{
		// Check if the the request already contains a UUID for the organization
		$id = $this->setId($request);

		// Store the new organization in the database
		$organization = Organization::create([
			"id" => $id,
			"user_id" => $this->user->id,
			"designation" => $request->designation
		]);

		// Also add the owner to the organization user role table in order to be able to store the subscription
		$this->user->organizations()->attach($organization->id, ['role_id' => 0]);

		// Send the invitations
		$invitations = $request->invitations;
		if ($invitations != NULL) {
			foreach ($invitations as $invitation) {
				$invitationService->send((object) $invitation, $organization, (string) Str::uuid(), $invitation['target_email']);
			}
		}

		return new OrganizationResource($organization);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Organization  $organization
	 * @return Response
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
	 *		name="organization_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-companies",
	 *		required=false,
	 *		in="header"
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
	 *		name="include-markers",
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
	 *		name="include-organization-subscription",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-organization-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-organization-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-organization-users-roles",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-company-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-company-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-project-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-project-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-company-image",
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
	public function show(Organization $organization)
	{
		// Check if the user is authorized to view the organization
		$this->authorize('view', $organization);

		return new OrganizationResource($organization);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  OrganizationUpdateRequest  $request
	 * @param  Organization  $organization
	 * @return Response
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
	public function update(OrganizationStoreRequest $request, Organization $organization)
	{
		// Check if the user is authorized to update the organization
		$this->authorize('update', $organization);

		// Update the organization
		$organization->update($request->all());

		// Update the corresponding stripe customer if one exists
		if ($organization->billingAddress) {
			$organization->billingAddress->updateStripeCustomer([
				'name' => $organization->designation
			]);
		}

		broadcast(new OrganizationUpdated($organization))->toOthers();

		return new OrganizationResource($organization);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Organization  $organization
	 * @return Response
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

		foreach($organization->billingAddress->subscriptions as $subscription) {
			$subscriptionItems = $subscription->items;

            $stripe = new StripeClient(config('app.stripe_api_secret'));

            $stripe->subscriptions->cancel(
                $subscription->stripe_id,
                []
            );

			// $organization->billingAddress->subscription($subscription->name)->cancel();

			foreach($subscriptionItems as $subscriptionItem) {
				$users = $organization->users;
				foreach($users as $user) {
					$organization->users()->where("subscription_item_id", $subscriptionItem)->updateExistingPivot($user->id, [
						'subscription_item_id' => NULL,
						'restricted_subscription_usage' => NULL,
						'assigned_on' => NULL
					]);
				}
			}
		}

		$val = $organization->delete();

		broadcast(new OrganizationUpdated($organization))->toOthers();

		return response($val, 204);
	}

	/**
	 * Display a list of users that belongs to the organization.
	 *
	 * @param  Organization  $organization
	 * @return Response
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
	 * 	@OA\Parameter(
	 *		name="include-users-organization-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-subscription-item",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-users-companies",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-users-company-role",
	 *		required=false,
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
		$this->authorize('view', $organization);

		return OrganizationUserRoleResource::collection(
			OrganizationUserRole::where("organization_id", $organization->id)->whereNot("role_id", 0)->get()
		);
	}

	/**
	 * Show a specific user that belongs to the organization.
	 *
	 * @param  Organization  $organization
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/organizations/{organization_id}/users/{user_id}",
	 *	tags={"Organization"},
	 *	summary="A specific organization user.",
	 *	operationId="showOrganizationsUser",
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
	 * 	@OA\Parameter(
	 *		name="include-users-organization-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-subscription-item",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-users-companies",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-users-company-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-users-projects",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-users-project-role",
	 *		required=false,
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
	public function user(Organization $organization, User $user)
	{
		// Check if the user is authorized to view the users of the organization
		$this->authorize('viewUser',  [$organization, $user]);

		$organizationUserRole = OrganizationUserRole::where("organization_id", $organization->id)->where('user_id', $user->id)->first();

		if (!isset($organizationUserRole)) {
			return response()->json(["data" => [
				"message" => __("application.organization-user-not-found", ['organization' => __("data.organization")])
			]], 409);
		}

		return new OrganizationUserRoleResource($organizationUserRole);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  OrganizationUserRoleUpdateRequest  $request
	 * @param  Organization  $organization
	 * @param  User  $user
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/organizations/{organization_id}/users/{user_id}",
	 *	tags={"Organization"},
	 *	summary="Update a users role in a given organization.",
	 *	operationId="updateOrganizationUserRole",
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
	 *                  description="The id of the new role",
	 *                  property="role_id",
	 *                  type="integer",
	 *              ),
	 *              required={"role_id"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/OrganizationUserRole"
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
	public function updateUserRole(OrganizationUserRoleUpdateRequest $request, Organization $organization, User $user)
	{
		// Check if the user is authorized to update the users role in the given organization
		$this->authorize('updateUserRole', $organization);

		$organizationUserRole = OrganizationUserRole::where("organization_id", $organization->id)->where('user_id', $user->id)
			->with('organization')
			->with('user')
			->with('role')
			->first();

		if (!isset($organizationUserRole)) {
			return response()->json(["data" => [
				"message" => __("application.organization-user-not-found", ['organization' => __("data.organization")])
			]], 409);
		}

		// Update the organizations user role
		$organization->users()->updateExistingPivot($user->id, [
			'role_id' => $request->role_id
		]);

		return new OrganizationUserRoleResource(OrganizationUserRole::where('organization_id', $organization->id)->where('user_id', $user->id)->first());
	}

	/**
	 * Remove a user from the organization
	 *
	 * @param  Organization  $organization
	 * @return Response
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

		broadcast(new OrganizationUserRemoved($user, $organization))->toOthers();

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
	 * 	@OA\Parameter(
	 *		name="status-id",
	 *		required=false,
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
	public function invitations(Request $request, Organization $organization)
	{
		// Check if the user is authorized to view the invitations of the organization
		$this->authorize('viewInvitations', $organization);

		// Check if the request contains a status_id so only those invitations are returned
		$header = $request->header();
		if (array_key_exists('status-id', $header) && $header['status-id'][0] != '') {
			$invitations = $organization->invitations()->where('status_id', $header['status-id'][0])->get();
		} else {
			$invitations = $organization->invitations;
		}

		return InvitationResource::collection($invitations);
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
		if (!$organization->invitations->where('target_email', $recipient_mail)->where('status_id', 1)->isEmpty() || $organization->users->contains($recipient)) {
			return response()->json(["data" => [
				"message" => __('application.organization-user-already-invited')
			]], 409);
		}

		$id = $this->setId($request);
		$invitation = $invitationService->send($request, $organization, $id, $recipient_mail);

		broadcast(new InvitationCreated($invitation))->toOthers();

		return new InvitationResource($invitation);
	}
}
