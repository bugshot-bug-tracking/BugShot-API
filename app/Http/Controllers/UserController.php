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
use App\Models\OrganizationUserRole;
use App\Models\CompanyUserRole;
use App\Models\ProjectUserRole;
use App\Models\BugUserRole;

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
	 * 	@OA\Parameter(
	 *		name="include-subscriptions",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-notifications",
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

		$email = $user->email;

		// Check if the request comes with an image and if so, store it
		$image = $user->image;
		if ($request->base64 != NULL) {
			$image = $imageService->store($request->base64, $image);
			$this->user->image()->save($image);
		}

		// Update the user
		$user->update($request->all());
		if ($request->has('password')) {
			$user->update([
				'password' => $request->password ? Hash::make($request->password) : null
			]);
		}

		// Update the corresponding stripe customer
		if($user->billingAddress) {
			$user->billingAddress->updateStripeCustomer([
				'name' => $user->first_name . ' ' . $user->last_name,
				'email' => $user->email
			]);
		}

		// Check if the email of the user changed and if so, update the email addresses of all organizations the user created
		if ($email != $user->email) {
			foreach ($user->createdOrganizations as $organization) {
				if ($organization->billingAddress) {
					$organization->billingAddress->updateStripeCustomer([
						'email' => $user->email
					]);
				}
			}
		}

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

		// Remove user from all resources he is part of
		OrganizationUserRole::where("user_id", $user->id)->delete();
		CompanyUserRole::where("user_id", $user->id)->delete();
		ProjectUserRole::where("user_id", $user->id)->delete();
		BugUserRole::where("user_id", $user->id)->delete();

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
		// Check if the user is authorized to view the project
		$this->authorize('checkProject', $user);

		// Get all projects where the user is a part of or has created
		$projects = $user->projects->merge($user->createdProjects);

		// Initialize collections for exact and additional projects
		$exactProjects = collect();
		$additionalProjects = collect();

		// Iterate through each project
		foreach ($projects as $tempProject) {
			// Merge the project URL and its associated URLs into a single array
			$projectUrls = [$tempProject->url, ...$tempProject->urls->pluck('url')->toArray()];
			
			// Check if the requested URL matches the project URL origin or a wildcard URL pattern
			foreach ($projectUrls as $url) {
				// Check if the URL contains a wildcard character *
				if(str_contains($url, "*")){
					// If the URL matches the wildcard URL pattern, add the project to the additionalProjects collection
					if($this->matchWildcardUrl($request->url, $url) || $this->matchWildcardUrlOrigin($request->url, $url) )
					{
						$additionalProjects->push($tempProject);
						break;
					}
				}
				else{
					// If the URL is an exact match, add the project to the exactProjects collection
					if(rtrim($request->url, '/') === rtrim($url, '/'))
					{
						$exactProjects->push($tempProject);
						break;
					}
					else{
						// If the URL origin matches the project url origin, add the project to the additionalProjects collection
						if($this->checkUrlOrigin($request->url, $url)){
							$additionalProjects->push($tempProject);
							break;
						}
					}
				}
			}
		}

		// Remove duplicate additional projects
		$uniqueProjects = $additionalProjects->unique('id');
		
		// Remove exact projects from additional projects
		$uniqueProjects = $uniqueProjects->diff($exactProjects);

		// Return JSON response with exact and additional projects
		return response()->json([
			'data' => [
				'exact' => ProjectResource::collection($exactProjects),
				'additional' => ProjectResource::collection($uniqueProjects),
			],
		]);
	}

	/**
	 * Check if two URLs have the same origin (scheme and host)
	 *
	 * @param string $url1 The first URL
	 * @param string $url2 The second URL
	 * @return bool True if both URLs have the same origin, otherwise false
	 */	private function checkUrlOrigin($url1, $url2)
	{
		// Check if the URLs are not empty or if they contain wildcard characters
		if (!$url1 || !$url2) {
			return false; // Return false if any of the conditions is true
		}
		
		// Parse the URLs
		$parsedUrl1 = parse_url($url1);
		$parsedUrl2 = parse_url($url2);
		
		// Check if both URLs have been parsed successfully and if both URLs have the same scheme and host
		return $parsedUrl1 && $parsedUrl2 && $parsedUrl1['scheme'] == $parsedUrl2['scheme'] && $parsedUrl1['host'] == $parsedUrl2['host'];
	}

	// Match a URL against a wildcard URL pattern
	private function matchWildcardUrl($url, $pattern)
	{
		// Replace * with a regular expression pattern that matches any characters
		$pattern = str_replace('\*', '.*', preg_quote(rtrim($pattern, '/'), '/'));
		// Use regular expression string matching to determine if the URL matches the pattern
		return preg_match('/^' . $pattern . '\/*$/', $url);
	}

	// Match a URL against a wildcard URL pattern
	private function matchWildcardUrlOrigin($url, $pattern)
	{
		// Check if the URLs are not empty or if they contain wildcard characters
		if (!$url || !$pattern) {
			return false; // Return false if any of the conditions is true
		}

		$url_with_protocol = '';
		if(str_starts_with($pattern, "*")){
			$url_with_protocol = str_replace("*://", "http://", $pattern);
		}
		else{
			$url_with_protocol = $pattern;
		}
		
		// Parse the URLs
		$parsedUrl1 = parse_url($url);
		$parsedUrl2 = parse_url($url_with_protocol);
		
		// Check if both URLs have been parsed successfully and if both URLs have the same scheme and host
		return $parsedUrl1 && $parsedUrl2 && $this->matchWildcardUrl($parsedUrl1['host'], $parsedUrl2["host"]);
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
		$this->authorize('viewSettings', $user);

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
	 *	path="/users/{user_id}/settings/{setting_id}",
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
		$this->authorize('updateSetting', $user);

		// Update the users setting
		$user->settings()->updateExistingPivot($setting, array('value_id' => $request->value_id), false);

		return new SettingUserValueResource(SettingUserValue::where('setting_id', $setting->id)->where('user_id', $user->id)->first());
	}


	/**
	 * Start a trial for the given user
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/users/{user_id}/start-trial",
	 *	tags={"User"},
	 *	summary="Start a trial for the user.",
	 *	operationId="userStartTrial",
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
	 *		description="Success"
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
	public function startTrial(User $user)
	{
		// Check if the user is authorized to start the trial
		$this->authorize('view', $user);

		$user->startTrial();

		return response()->json("Trial started succesfully", 200);
	}
}
