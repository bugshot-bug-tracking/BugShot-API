<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use App\Http\Requests\CheckProjectRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Resources
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ImageResource;

// Services
use App\Services\ImageService;

// Models
use App\Models\User;

// Requests
use App\Http\Requests\UserRequest;

/**
 * @OA\Tag(
 *     name="User",
 * )
 */
class UserController extends Controller
{
    /**
	 * @OA\Get(
	 *	path="/users",
	 *	tags={"User"},
	 *	summary="All users.",
	 *	operationId="allUsers",
	 *	security={ {"sanctum": {} }},
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Check if the user is authorized to retrieve a list of users
		$this->authorize('viewAny', [User::class]);

        return UserResource::collection(User::all());
    }

	/**
	 * @OA\Post(
	 *	path="/users",
	 *	tags={"User"},
	 *	summary="Store one user.",
	 *	operationId="storeUser",
	 *	security={ {"sanctum": {} }},
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
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
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

        return new UserResource($user);
    }

	/**
	 * @OA\Get(
	 *	path="/users/{user_id}",
	 *	tags={"User"},
	 *	summary="Show one user.",
	 *	operationId="showUser",
	 *	security={ {"sanctum": {} }},
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
    /**
     * Display the specified resource.
     *
	 * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
		// Check if the user is authorized to view the user
		$this->authorize('view', $user);

		return new UserResource($user);
    }

	/**
	 * @OA\Put(
	 *	path="/users/{user_id}",
	 *	tags={"User"},
	 *	summary="Update a user.",
	 *	operationId="updateUser",
	 *	security={ {"sanctum": {} }},

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
	 *              required={"first_name","last_name","email","password","password_confirmation"}
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
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user, ImageService $imageService)
    {
		// Check if the user is authorized to update the user
		$this->authorize('update', $user);

        // Check if the request comes with an image and if so, store it
		$image = $user->image;
		if($request->base64 != NULL) {
			$image = $imageService->store($request->base64, $image);
			$this->user->image()->save($image);
		}

		// Create the user
		$user->update([
			'first_name' => $request->first_name,
			'last_name' => $request->last_name,
			'email' => $request->email,
			'password' => Hash::make($request->password)
		]);

		return new UserResource($user);
    }

	/**
	 * @OA\Delete(
	 *	path="/users/{user_id}",
	 *	tags={"User"},
	 *	summary="Delete a user.",
	 *	operationId="deleteUser",
	 *	security={ {"sanctum": {} }},
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
    /**
     * Remove the specified resource from storage.
     *
	 * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
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
	 * @OA\Get(
	 *	path="/users/{user_id}/image",
	 *	tags={"User"},
	 *	summary="User image.",
	 *	operationId="showUserImage",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Display the image that belongs to the user.
	 *
	 * @param  \App\Models\User  $user
	 * @return \Illuminate\Http\Response
	*/
	public function image(User $user)
	{
		// Check if the user is authorized to view the image of a user
		$this->authorize('view', $user);

		return new ImageResource($user->image);
	}

	/**
	 * @OA\Post(
	 *	path="/users/{user_id}/check-project",
	 *	tags={"User"},
	 *	summary="Return a project with the specified url where the user is a part of",
	 *	operationId="checkProject",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Check if url belongs to a project of the user
	 *
	 * @param  \App\Models\User  $user
	 * @return \Illuminate\Http\Response
	*/
	public function checkProject(CheckProjectRequest $request, User $user)
	{
		// Check if the user is authorized to view the image of a user
		$this->authorize('checkProject', $user);

		$projects = $user->projects->where('url', $request->url);

		return ProjectResource::collection($projects);
	}
}
