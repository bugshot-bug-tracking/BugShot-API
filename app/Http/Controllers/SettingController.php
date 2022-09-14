<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\CompanyUserRoleResource;
use App\Http\Resources\SettingResource;
use App\Http\Resources\ProjectUserRoleResource;

// Models
use App\Models\Company;
use App\Models\CompanyUserRole;
use App\Models\Setting;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectUserRole;

/**
 * @OA\Tag(
 *     name="Setting",
 * )
 */
class SettingController extends Controller
{

	/**
	 * @OA\Get(
	 *	path="/settings",
	 *	tags={"Setting"},
	 *	summary="Show all settings.",
	 *	operationId="showSettings",
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
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Setting")
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
		return SettingResource::collection(Setting::all());
	}

	/**
     * Store a newly created resource in storage.
     *
     * @param  UserStoreRequest  $request
     * @return Response
     */
	/**
	 * @OA\Post(
	 *	path="/settings",
	 *	tags={"Setting"},
	 *	summary="Store one setting.",
	 *	operationId="storeSetting",
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
	 * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="first_name",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="last_name",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="email",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="password",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="password_confirmation",
	 *                  type="string",
	 *              ),
	 *              required={"first_name","last_name","email","password","password_confirmation"}
	 *          )
	 *      )
	 *  ),
	 * 
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Setting")
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
    public function store(UserStoreRequest $request)
    {
        // Check if the user is authorized to create a new user
		$this->authorize('create', [User::class]);

        // Check if the the request already contains a UUID for the user
		$id = $this->setId($request);

        // Create the user
        $user = User::create([
            'id' => $id,
			'first_name' => $request->first_name,
			'last_name' => $request->last_name,
			'email' => $request->email,
			'password' => Hash::make($request->password)
		]);

		// TODO: Stripe update
		// Update the corresponding Stripe customer
		// $response = Http::put(config('app.payment_url') . '/users/' . $user->id, [
		// 	'user' => $user
		// ]);

		// $response->throw();

        return new UserResource($user);
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Setting  $setting
	 * @return \App\Http\Resources\SettingResource
	 */
	/**
	 * @OA\Get(
	 *	path="/settings/{invitation_id}",
	 *	tags={"Setting"},
	 *	summary="Show one setting.",
	 *	operationId="showSetting",
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
	 *			ref="#/components/schemas/Setting/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Setting"
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
	public function show(User $user, Setting $setting)
	{
		// Check if the user is authorized to view the setting
		$this->authorize('view', $setting);

		return new SettingResource($setting);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Setting  $setting
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/settings/{invitation_id}",
	 *	tags={"Setting"},
	 *	summary="Delete a setting.",
	 *	operationId="deleteSetting",
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
	 *			ref="#/components/schemas/Setting/properties/id"
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
	public function destroy(User $user, Setting $setting)
	{
		// Check if the user is authorized to delete the setting
		$this->authorize('delete', $setting);

		$val = $setting->delete();

		return response($val, 204);
	}

	/**
	 * Update the resource to a new status.
	 *
	 * @param  \App\Models\Setting  $setting
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Get(
	 *	path="/settings/{invitation_id}/accept",
	 *	tags={"Setting"},
	 *	summary="Accept one setting.",
	 *	operationId="acceptSetting",
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
	 *			ref="#/components/schemas/Setting/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Setting"
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
	public function accept(User $user, Setting $setting)
	{
		// Check if the user is authorized to accept the setting
		$this->authorize('accept', $setting);

		if (Auth::user()->email !== $setting->target_email)
			return response()->json([
				"errors" => [
					"status" => 403,
					"details" => __('application.setting-not-for-you')
				]
			], 403);

		if ($setting->status_id !== 1)
			return response()->json(["data" => [
				"message" => __('application.setting-already-in-progress')
			]], 288);

		$invitable = $setting->invitable;

		switch ($setting->invitable_type) {
			case 'company':
				return $this->acceptCompany($user, $setting, $invitable);
				break;

			case 'project':
				return $this->acceptProject($user, $setting, $invitable);
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
	 * @param  \App\Models\Setting  $setting
	 * @return \Illuminate\Http\Response
	 */
	/**
	 * @OA\Get(
	 *	path="/settings/{invitation_id}/decline",
	 *	tags={"Setting"},
	 *	summary="Decline one setting.",
	 *	operationId="declineSetting",
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
	 *			ref="#/components/schemas/Setting/properties/id"
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
	public function decline(User $user, Setting $setting)
	{
		// Check if the user is authorized to decline the setting
		$this->authorize('decline', $setting);

		if (Auth::id() !== $setting->target_email)
			return response()->json([
				"errors" => [
					"status" => 403,
					"details" => __('application.setting-not-for-you')
				]
			], 403);

		if ($setting->status_id !== 1)
			return response()->json(["data" => [
				"message" => __('application.setting-already-in-progress')
			]], 288);

		$setting->update(["status_id" => 3]);
		return response()->json("", 204);
	}

	/**
	 * Generate the link between user, company and role.
	 *
	 * @param  \App\Models\User  $user
	 * @param  \App\Models\Setting  $setting
	 * @param  \App\Models\Company  $company
	 * @return \App\Http\Resources\CompanyUserRoleResource
	 */
	private function acceptCompany(User $user, Setting $setting, Company $company)
	{
		// Check if the user is already part of this company
		if ($user->companies->find($company) !== NULL) {
			$setting->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => __('application.already-part-of-the-company')
			]], 288);
		}

		$user->companies()->attach($company->id, ['role_id' => $setting->role_id]);
		$setting->update(["status_id" => 2]);

		return new CompanyUserRoleResource(CompanyUserRole::where('company_id', $company->id)->first());
	}

	/**
	 * Generate the link between user, project and role.
	 * And if needed between user, company and role.
	 * @param  \App\Models\User  $user
	 * @param  \App\Models\Setting  $setting
	 * @param  \App\Models\Project  $project
	 * @return \App\Http\Resources\ProjectUserRoleResource
	 */
	private function acceptProject(User $user, Setting $setting, Project $project)
	{
		// Check if the user is already part of this project
		if ($user->projects->find($project) !== NULL) {
			$setting->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => __('application.already-part-of-the-project')
			]], 288);
		}

		$user->projects()->attach($project->id, ['role_id' => $setting->role_id]);

		// Check if the user is already part of this company
		if ($user->companies->find($project->company) == NULL) {
			$user->companies()->attach($project->company->id, ['role_id' => $setting->role_id]);
		}

		$setting->update(["status_id" => 2]);

		return new ProjectUserRoleResource(ProjectUserRole::where('project_id', $project->id)->first());
	}
}
