<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyInviteRequest;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyUserRoleResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\InvitationResource;
use App\Services\ImageService;
use App\Models\Company;
use App\Models\Image;
use App\Models\CompanyUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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
		// Check if the request includes a timestamp and query the companies accordingly
        if($request->timestamp == NULL) {
            $companies = Auth::user()->companies;
        } else {
            $companies = Auth::user()->companies->where([
                ["companies.updated_at", ">", date("Y-m-d H:i:s", $request->timestamp)]
            ]);
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
        if($request->id == NULL) {
            $id = (string) Str::uuid();
        } else {
            $id = $request->id;
        }

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
		CompanyUserRole::create([
			"company_id" => $company->id,
			"user_id" => Auth::id(),
			"role_id" => 1 // Owner
		]);

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
		// Check if the company comes with an image (or a color)
		$image = $company->image;
		if($request->base64 != NULL) {
			$image = $imageService->store($request->base64, $image);
			$image != false ? $company->image()->save($image) : true;
			$color_hex = NULL;
		} else {
			$imageService->destroy($image);
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
	public function destroy(Company $company)
	{
		$val = $company->update([
			"deleted_at" => new \DateTime()
		]);
		
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
		$company_user_roles = CompanyUserRoleResource::collection(
			CompanyUserRole::where("company_id", $company->id)
				->with('company')
				->with('user')
				->with("role")
				->get()
		);

		return response()->json($company_user_roles, 200);
	}

	/**
	 * @OA\Post(
	 *	path="/companies/{company_id}/invite",
	 *	tags={"Company"},
	 *	summary="Invite a user to the company and asign it a role",
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
	 *                  description="The invited user id.",
	 *                  property="target_id",
	 *					type="integer",
	 *                  format="int64",
	 *              ),
	 *              @OA\Property(
	 *                  description="The invited user role.",
	 *                  property="role_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *              required={"target_id","role_id"}
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
	 *		response=422,
	 *		description="Unprocessable Entity"
	 *	),
	 * )
	 **/
	public function invite(Company $company, CompanyInviteRequest $request)
	{
		$inputs = $request->all();
		$inputs['sender_id'] = Auth::id();
		$inputs['status_id'] = 1;

		return new InvitationResource($company->invitations()->create($inputs));
	}
}
