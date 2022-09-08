<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use App\Http\Requests\CheckProjectRequest;
use Illuminate\Support\Facades\Hash;

// Resources
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\SettingUserValueResource;

// Services
use App\Services\ImageService;

// Models
use App\Models\User;
use App\Models\Setting;
use App\Models\SettingUserValue;

// Requests
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\SettingRequest;
use App\Http\Requests\UserBillingAddressStoreRequest;

/**
 * @OA\Tag(
 *     name="User",
 * )
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    /**
	 * @OA\Get(
	 *	path="/users",
	 *	tags={"User"},
	 *	summary="All users.",
	 *	operationId="allUsers",
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
	 *			@OA\Items(ref="#/components/schemas/User")
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
    public function index()
    {
        // Check if the user is authorized to retrieve a list of users
		$this->authorize('viewAny', [User::class]);

        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserStoreRequest  $request
     * @return Response
     */
	/**
	 * @OA\Post(
	 *	path="/users",
	 *	tags={"User"},
	 *	summary="Store one user.",
	 *	operationId="storeUser",
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
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/User"
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

		// Create the corresponding stripe customer
		$user->createOrGetStripeCustomer(['name' => $user->first_name . ' ' . $user->last_name]);

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
	 * @param  User  $user
     * @return Response
     */
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}",
	 *	tags={"User"},
	 *	summary="Show one user.",
	 *	operationId="showUser",
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
	 *			ref="#/components/schemas/User"
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
    public function show(User $user)
    {
		// Check if the user is authorized to view the user
		$this->authorize('view', $user);

		return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserUpdateRequest  $request
	 * @param  User  $user
     * @return Response
     */
	/**
	 * @OA\Put(
	 *	path="/users/{user_id}",
	 *	tags={"User"},
	 *	summary="Update a user.",
	 *	operationId="updateUser",
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
	 * 	  			@OA\Property(
	 *                  property="old_password",
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
	 * 	   			@OA\Property(
	 *                  property="base64",
	 *                  type="string"
	 *              ),
	 *              required={"first_name","last_name","email", "old_password"}
	 *          )
	 *      )
	 *  ),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/User"
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
    public function update(UserUpdateRequest $request, User $user, ImageService $imageService)
    {
		// Check if the user is authorized to update the user
		$this->authorize('update', $user);
		
        // Check if the request comes with an image and if so, store it
		$image = $user->image;
		if($request->base64 != NULL) {
			$image = $imageService->store($request->base64, $image);
			$this->user->image()->save($image);
		}

		// Update the user
		$user->update($request->all());
		if($request->has('password')) {
			$user->update([
				'password' => $request->password ? Hash::make($request->password) : null
			]);
		}

		// Update the corresponding stripe customer 
		$user->updateStripeCustomer([
			'name' => $user->first_name . ' ' . $user->last_name,
			'email' => $user->email
		]);

		return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
	 * @param  User  $user
     * @return Response
     */
	/**
	 * @OA\Delete(
	 *	path="/users/{user_id}",
	 *	tags={"User"},
	 *	summary="Delete a user.",
	 *	operationId="deleteUser",
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
    public function destroy(User $user, ImageService $imageService)
    {
		// Check if the user is authorized to delete the user
		$this->authorize('delete', $user);    
	
		$val = $user->delete();
		
		// Delete the respective image if present
		$imageService->delete($user->image);

		return response($val, 204);
	}

	/**
	 * Display the image that belongs to the user.
	 *
	 * @param  User  $user
	 * @return Response
	*/
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/image",
	 *	tags={"User"},
	 *	summary="User image.",
	 *	operationId="showUserImage",
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
	public function image(User $user)
	{
		// Check if the user is authorized to view the image of a user
		$this->authorize('view', $user);

		return new ImageResource($user->image);
	}

	/**
	 * Check if url belongs to a project of the user
	 *
	 * @param  User  $user
	 * @return Response
	*/
	/**
	 * @OA\Post(
	 *	path="/users/{user_id}/check-project",
	 *	tags={"User"},
	 *	summary="Return a project with the specified url where the user is a part of",
	 *	operationId="checkProject",
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
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *              required={"url"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/ProjectUserRole")
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
	public function checkProject(CheckProjectRequest $request, User $user)
	{
		// Check if the user is authorized to view the image of a user
		$this->authorize('checkProject', $user);

		// $userIsPriviliegated = $this->user->isPriviliegated('companies', $company);
		
		// Check if the request includes a timestamp and query the projects accordingly
		$projects = $user->projects->where('url', $request->url);
		$createdProjects = $user->createdProjects->where('url', $request->url);
		// Combine the two collections
		$projects = $projects->concat($createdProjects);

		// $projects = $user->projects->where('url', $request->url);
		
		return ProjectResource::collection($projects);
	}

	/**
	 * Display a list of users that belongs to the company.
	 *
	 * @param  Company  $company
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/settings",
	 *	tags={"User"},
	 *	summary="All user settings.",
	 *	operationId="allUserSettings",
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
	 *			@OA\Items(ref="#/components/schemas/SettingUserValue")
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
	public function settings(User $user)
	{
		// Check if the user is authorized to view the settings of the given user
		// $this->authorize('view', $company);

		return SettingUserValueResource::collection(
			SettingUserValue::where("user_id", $user->id)
				->with('user')
				->with('setting')
				->with('value')
				->get()
		);
	}

	/**
     * Store a new setting of the user.
     *
     * @param  UserSettingUpdateRequest  $request
     * @return Response
     */
	/**
	 * @OA\Put(
	 *	path="/users/{user_id}/settings/{setting}",
	 *	tags={"User"},
	 *	summary="Update a users setting.",
	 *	operationId="updateUserSetting",
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
	 *	@OA\Parameter(
	 *		name="setting_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Setting/properties/id"
	 *		)
	 *	),
	 *
	 * 	@OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The selected value for the setting.",
	 *                  property="value_id",
	 *                  type="integer",
	 *                  format="int64"
	 *              ),
	 *              required={"value_id"}
	 *          )
	 *      )
	 *  ),
	 * 
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/SettingUserValue")
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
	public function updateSetting(SettingRequest $request, User $user, Setting $setting)
	{
		// Check if the user is authorized to update the setting of the given user
		// $this->authorize('update', $project);

		// Update the project
		$userSetting = $user->settings->where('setting_id', $setting->id)->firstOrFail();
		dd($userSetting);
		// $userSetting->update([
		// 	"value_id" => $company->id,
		// 	"color_hex" => $color_hex,
        //     "url" => substr($request->url, -1) == '/' ? substr($request->url, 0, -1) : $request->url // Check if the given url has "/" as last char and if so, store url without it
		// ]);

		// return new ProjectResource($project);
	}
}
