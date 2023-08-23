<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...

use App\Events\CompanyCreated;
use App\Events\CompanyDeleted;
use App\Events\CompanyUpdated;
use App\Events\CompanyUserRemoved;
use App\Events\CompanyUserUpdated;
use App\Events\InvitationCreated;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\CompanyResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\CompanyUserRoleResource;

// Services
use App\Services\ImageService;
use App\Services\InvitationService;

// Models
use App\Models\Organization;
use App\Models\Company;
use App\Models\User;
use App\Models\CompanyUserRole;
use App\Models\SettingUserValue;

// Requests
use App\Http\Requests\CompanyStoreRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Http\Requests\InvitationRequest;
use App\Http\Requests\CompanyUserRoleUpdateRequest;

/**
 * @OA\Tag(
 *     name="Company",
 * )
 */
class CompanyController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/organizations/{organization_id}/companies",
	 *	tags={"Company"},
	 *	summary="All companies.",
	 *	operationId="allCompanies",
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
	 *		name="organization_id",
	 *		required=true,
     *      example="AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 *
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
	 *		name="only-assigned-bugs",
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
	 *		name="include-company-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-company-users-roles",
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
	 *		name="include-project-image",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachment-base64",
	 *		required=false,
	 *		in="header"
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Company")
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

	public function index(Request $request, Organization $organization)
	{
		// Check if the user is authorized to list the companies of the organization
		$this->authorize('viewAny', [Company::class, $organization]);

		// Get timestamp
		$timestamp = $request->header('timestamp');
		$userIsPriviliegated = $this->user->isPriviliegated('organizations', $organization);

		// Check if the request includes a timestamp and query the companies accordingly
		if($timestamp == NULL) {
			if($userIsPriviliegated) {
				$companies = $organization->companies;
			} else {
				$companies = Auth::user()->companies->where('organization_id', $organization->id);
				$createdCompanies = $this->user->createdCompanies->where('organization_id', $organization->id);
				// Combine the two collections
				$companies = $companies->concat($createdCompanies);
			}
        } else {
			if($userIsPriviliegated) {
				$companies = $organization->companies->where("companies.updated_at", ">", date("Y-m-d H:i:s", $timestamp));
			} else {
				$companies = Auth::user()->companies
					->where("companies.updated_at", ">", date("Y-m-d H:i:s", $timestamp))
					->where('organization_id', $organization->id);
				$createdCompanies = $this->user->createdCompanies
					->where("companies.updated_at", ">", date("Y-m-d H:i:s", $timestamp))
					->where('organization_id', $organization->id);

				// Combine the two collections
				$companies = $companies->concat($createdCompanies);
			}
        }

		return CompanyResource::collection($companies->sortBy('designation'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  CompanyStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/organizations/{organization_id}/companies",
	 *	tags={"Company"},
	 *	summary="Store one company.",
	 *	operationId="storeCompany",
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
	 *		name="organization_id",
	 *		required=true,
     *      example="AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The company name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  description="The hexcode of the color (optional)",
	 *                  property="color_hex",
	 * 					type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The base64 string of the image belonging to the company (optional)",
	 *                  property="base64",
	 *                  type="string",
	 *              ),
	 *    			@OA\Property(
	 *                  property="invitations",
	 *                  type="array",
	 * 					@OA\Items(
	 *              		@OA\Property(
	 *              		    description="The invited user email.",
	 *              		    property="target_email",
	 *							type="string"
	 *              		),
	 *              		@OA\Property(
	 *              		    description="The invited user role.",
	 *              		    property="role_id",
	 *              		    type="integer",
	 *              		    format="int64"
	 *              		),
	 * 					)
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
	 *			ref="#/components/schemas/Company"
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
	public function store(CompanyStoreRequest $request, Organization $organization, ImageService $imageService, InvitationService $invitationService)
	{
		// Check if the user is authorized to create the company
		$this->authorize('create', [Company::class, $organization]);

		// Check if the the request already contains a UUID for the company
		$id = $this->setId($request);

		// Store the new company in the database
		$company = $organization->companies()->create([
			"id" => $id,
			"user_id" => $this->user->id,
			"designation" => $request->designation,
			"color_hex" => $request->color_hex,
		]);

		// Also add the owner to the company user role table
		$this->user->companies()->attach($company->id, ['role_id' => 0]);

		// Check if the company comes with an image (or a color)
		$image = NULL;
		if($request->base64 != NULL) {
			$image = $imageService->store($request->base64, $image);
			$company->image()->save($image);
		}

		// Send the invitations
		$invitations = $request->invitations;
		if($invitations != NULL) {
			foreach($invitations as $invitation) {
				$invitationService->send((object) $invitation, $company, (string) Str::uuid(), $invitation['target_email']);
			}
		}

		broadcast(new CompanyCreated($company))->toOthers();

		return new CompanyResource($company);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Company  $company
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/organizations/{organization_id}/companies/{company_id}",
	 *	tags={"Company"},
	 *	summary="Show one company.",
	 *	operationId="showCompany",
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
	 *		name="organization_id",
	 *		required=true,
     *      example="AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
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
	 *		name="only-assigned-bugs",
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
	 *			ref="#/components/schemas/Company"
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
	public function show(Organization $organization, Company $company)
	{
		// Check if the user is authorized to view the company
		$this->authorize('view', $company);

		return new CompanyResource($company);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  CompanyUpdateRequest  $request
	 * @param  Company  $company
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/organizations/{organization_id}/companies/{company_id}",
	 *	tags={"Company"},
	 *	summary="Update a company.",
	 *	operationId="updateCompany",
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
	 *		name="organization_id",
	 *		required=true,
     *      example="AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
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
	 *                  description="The company name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  description="The hexcode of the color (optional)",
	 *                  property="color_hex",
	 * 					type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The base64 string of the image belonging to the company (optional)",
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
	 *			ref="#/components/schemas/Company"
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
	public function update(CompanyUpdateRequest $request, Organization $organization, Company $company, ImageService $imageService)
	{
		// Check if the user is authorized to update the company
		$this->authorize('update', $company);

		// Check if the company comes with an image (or a color)
		$image = $company->image;

		if($request->base64 != NULL && $request->base64 != 'true') {
			$image = $imageService->store($request->base64, $image);
			$image != false ? $company->image()->save($image) : true;
			$color_hex = $company->color_hex; // Color stays the same
		} else {
			$imageService->delete($image);
			$color_hex = $request->color_hex;
		}

		// Apply default color if color_hex is null
		$color_hex = $color_hex == NULL ? '#7A2EE6' : $color_hex;

		// Update the company
		$company->update($request->all());
		$company->update([
			'color_hex' => $color_hex
		]);

		broadcast(new CompanyUpdated($company))->toOthers();

		return new CompanyResource($company);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Company  $company
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/organizations/{organization_id}/companies/{company_id}",
	 *	tags={"Company"},
	 *	summary="Delete a company.",
	 *	operationId="deleteCompany",
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
	 *		name="organization_id",
	 *		required=true,
     *      example="AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Organization/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
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
	public function destroy(Organization $organization, Company $company, ImageService $imageService)
	{
		// Check if the user is authorized to delete the company
		$this->authorize('delete', $company);

		$val = $company->delete();
		broadcast(new CompanyDeleted($company))->toOthers();

		// Delete the respective image if present
		$imageService->delete($company->image);

		return response($val, 204);
	}

	/**
	 * Display the image that belongs to the company.
	 *
	 * @param  Company  $company
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/image",
	 *	tags={"Company"},
	 *	summary="Company image.",
	 *	operationId="showCompanyImage",
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
	 *		name="company_id",
	 *		required=true,
	 *		example="BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB",
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
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
	public function image(Company $company, ImageService $imageService)
	{
		// Check if the user is authorized to view the image of the company
		$this->authorize('view', $company);

		return new ImageResource($company->image);
	}

	/**
	 * Display a list of users that belongs to the company.
	 *
	 * @param  Company  $company
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/users",
	 *	tags={"Company"},
	 *	summary="All company users.",
	 *	operationId="allCompaniesUsers",
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
	 *		name="include-projects",
	 *		required=false,
	 *		in="header"
	 *	),
	 *
	 *	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/CompanyUserRole")
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
	public function users(Company $company)
	{
		// Check if the user is authorized to view the users of the company
		$this->authorize('view', $company);

		return CompanyUserRoleResource::collection(
			CompanyUserRole::where('company_id', $company->id)->get()
		);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  CompanyUserRoleUpdateRequest  $request
	 * @param  Company  $company
	 * @param  User  $user
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/companies/{company_id}/users/{user_id}",
	 *	tags={"Company"},
	 *	summary="Update a users role in a given company.",
	 *	operationId="updateCompanyUserRole",
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
	 *		name="include-users-company-role",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
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
	 *			ref="#/components/schemas/CompanyUserRole"
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
	public function updateUserRole(CompanyUserRoleUpdateRequest $request, Company $company, User $user)
	{
		// Check if the user is authorized to update the users role in the given company
		$this->authorize('updateUserRole', $company);

		// Update the companies user role
		$company->users()->updateExistingPivot($user->id, [
			'role_id' => $request->role_id
		]);

		broadcast(new CompanyUserUpdated($user, $company))->toOthers();

		return new CompanyUserRoleResource(CompanyUserRole::where('company_id', $company->id)->where('user_id', $user->id)->first());
	}

	/**
	 * Remove a user from the company
	 *
	 * @param  Company  $company
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/companies/{company_id}/users/{user_id}",
	 *	tags={"Company"},
	 *	summary="Remove user from the company.",
	 *	operationId="removeCompanyUser",
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
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
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
	public function removeUser(Company $company, User $user)
	{
		// replace with approval request procedure
		if((Auth::id()!==$user->id))
			// Check if the user is authorized to view the users of the company
			$this->authorize('removeUser', $company);

		$val = $company->users()->detach($user);
		broadcast(new CompanyUserRemoved($user, $company))->toOthers();

		// Also remove the user from the related project
		// Commented out right now because we want that the user can stay in the projects while beeing removed from the company
		// $projects = $user->projects()->where('company_id', $company->id)->get();
		// foreach($projects as $project) {
		// 	$project->users()->detach($user);
		// }

		return response($val, 204);
	}

	/**
	 * Display a list of invitations that belongs to the company.
	 *
	 * @param  Company  $company
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/invitations",
	 *	tags={"Company"},
	 *	summary="All company invitations.",
	 *	operationId="allCompaniesInvitations",
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
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
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
	public function invitations(Request $request, Company $company)
	{
		// Check if the user is authorized to view the invitations of the company
		$this->authorize('viewInvitations', $company);

		// Check if the request contains a status_id so only those invitations are returned
		$header = $request->header();
		if(array_key_exists('status-id', $header) && $header['status-id'][0] != '') {
			$invitations = $company->invitations()->where('status_id', $header['status-id'][0])->get();
		} else {
			$invitations = $company->invitations;
		}

		return InvitationResource::collection($invitations);
	}

	/**
	 * @OA\Post(
	 *	path="/companies/{company_id}/invite",
	 *	tags={"Company"},
	 *	summary="Invite a user to the company and asign a role to him",
	 *	operationId="inviteCompany",
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
	 *		name="company_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Company/properties/id"
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
	public function invite(InvitationRequest $request, Company $company, InvitationService $invitationService)
	{
		// Check if the user is authorized to invite users to the company
		$this->authorize('invite', $company);

		// Check if the user has already been invited to the company or is already part of it
		$recipient_mail = $request->target_email;
		$recipient = User::where('email', $recipient_mail)->first();
		if(!$company->invitations->where('target_email', $recipient_mail)->where('status_id', 1)->isEmpty() || $company->users->contains($recipient)) {
			return response()->json(["data" => [
				"message" => __('application.company-user-already-invited')
			]], 409);
		}

		$id = $this->setId($request);
		$invitation = $invitationService->send($request, $company, $id, $recipient_mail);

		broadcast(new InvitationCreated($invitation))->toOthers();

		return new InvitationResource($invitation);
	}
}
