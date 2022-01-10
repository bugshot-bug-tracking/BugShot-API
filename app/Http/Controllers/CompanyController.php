<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\CompanyResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\CompanyUserRoleResource;

// Services
use App\Services\ImageService;
use App\Services\InvitationService;

// Models
use App\Models\Company;
use App\Models\User;
use App\Models\CompanyUserRole;

// Requests
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\InvitationRequest;

/**
 * @OA\Tag(
 *     name="Company",
 * )
 */
class CompanyController extends Controller
{
	/**
	 * @OA\Get(
	 *	path="/companies",
	 *	tags={"Company"},
	 *	summary="All companies.",
	 *	operationId="allCompanies",
	 *	security={ {"sanctum": {} }},
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
	 *		name="include-company-users",
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
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		// Get timestamp
		$timestamp = $request->header('timestamp');

		// Check if the request includes a timestamp and query the companies accordingly
        if($timestamp == NULL) {
            $companies = Auth::user()->companies->sortBy('designation');
        } else {
            $companies = Auth::user()->companies->where([
                ["companies.updated_at", ">", date("Y-m-d H:i:s", $timestamp)]
            ])->sortBy('designation');
        }
		
		return CompanyResource::collection($companies);
	}

	/**
	 * @OA\Post(
	 *	path="/companies",
	 *	tags={"Company"},
	 *	summary="Store one company.",
	 *	operationId="storeCompany",
	 *	security={ {"sanctum": {} }},
	 *
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

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\CompanyRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(CompanyRequest $request, ImageService $imageService)
	{	
		// Check if the the request already contains a UUID for the company
		$id = $this->setId($request);

		// Store the new company in the database
		$company = Company::create([
			"id" => $id,
			"designation" => $request->designation,
			"color_hex" => $request->color_hex,
		]);
		
		// Check if the company comes with an image (or a color)
		$image = NULL;
		if($request->base64 != NULL) {
			$image = $imageService->store($request->base64, $image);
			$company->image()->save($image);
		}

		// Store the respective role
		Auth::user()->companies()->attach($company->id, ['role_id' => 1]);

		return new CompanyResource($company);
	}

	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}",
	 *	tags={"Company"},
	 *	summary="Show one company.",
	 *	operationId="showCompany",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
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
	 *		name="include-company-users",
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
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function show(Company $company)
	{
		// Check if the user is authorized to view the company
		$this->authorize('view', $company);

		return new CompanyResource($company);
	}

	/**
	 * @OA\Put(
	 *	path="/companies/{company_id}",
	 *	tags={"Company"},
	 *	summary="Update a company.",
	 *	operationId="updateCompany",
	 *	security={ {"sanctum": {} }},

	 *	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
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
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\CompanyRequest  $request
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function update(CompanyRequest $request, Company $company, ImageService $imageService)
	{
		// Check if the user is authorized to update the company
		$this->authorize('update', $company);

		// Check if the company comes with an image (or a color)
		$image = $company->image;
		if($request->base64 != NULL && $request->base64 != 'true') {
			$image = $imageService->store($request->base64, $image);
			$image != false ? $company->image()->save($image) : true;
			$color_hex = $company->color_hex;
		} else {
			$imageService->delete($image);
			$color_hex = $request->color_hex;
		}

		// Update the company
		$company->update([
            'designation' => $request->designation,
			'color_hex' => $color_hex
        ]);
		
		return new CompanyResource($company);
	}

	/**
	 * @OA\Delete(
	 *	path="/companies/{company_id}",
	 *	tags={"Company"},
	 *	summary="Delete a company.",
	 *	operationId="deleteCompany",
	 *	security={ {"sanctum": {} }},

	 *	@OA\Parameter(
	 *		name="company_id",
	 *		required=true,
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
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Company $company, ImageService $imageService)
	{
		// Check if the user is authorized to delete the company
		$this->authorize('delete', $company);

		$val = $company->delete();
		
		// Delete the respective image if present
		$imageService->delete($company->image);

		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/image",
	 *	tags={"Company"},
	 *	summary="Company image.",
	 *	operationId="showCompanyImage",
	 *	security={ {"sanctum": {} }},
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
	 * Display the image that belongs to the company.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function image(Company $company, ImageService $imageService)
	{
		// Check if the user is authorized to view the image of the company
		$this->authorize('viewImage', $company);

		return new ImageResource($company->image);
	}

	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/users",
	 *	tags={"Company"},
	 *	summary="All company users.",
	 *	operationId="allCompaniesUsers",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Display a list of users that belongs to the company.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function users(Company $company)
	{
		// Check if the user is authorized to view the users of the company
		$this->authorize('viewUsers', $company);

		return CompanyUserRoleResource::collection(
			CompanyUserRole::where("company_id", $company->id)
				->with('company')
				->with('user')
				->with("role")
				->get()
		);
	}

	/**
	 * @OA\Get(
	 *	path="/companies/{company_id}/invitations",
	 *	tags={"Company"},
	 *	summary="All company invitations.",
	 *	operationId="allCompaniesInvitations",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Display a list of invitations that belongs to the company.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function invitations(Company $company)
	{
		// Check if the user is authorized to view the invitations of the company
		$this->authorize('viewInvitations', $company);
		
		return InvitationResource::collection($company->invitations);
	}

	/**
	 * @OA\Post(
	 *	path="/companies/{company_id}/invite",
	 *	tags={"Company"},
	 *	summary="Invite a user to the company and asign a role to him",
	 *	operationId="inviteCompany",
	 *	security={ {"sanctum": {} }},
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
		$targetUser = User::where('email', $request->target_email)->first();
		if($company->invitations->contains('target_email', $request->target_email) || $company->users->contains($targetUser)) {
			return response()->json(["data" => [
				"message" => "User has already been invited to the company or is already part of it."
			]], 409);
		}

		$id = $this->setId($request);

		$invitation = $invitationService->send($request, $company, $id);

		return new InvitationResource($invitation);
	}
}
