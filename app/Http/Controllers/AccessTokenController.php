<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Support\Facades\DB;
use App\Events\AccessTokenCreated;
use App\Events\AccessTokenDeleted;
use App\Events\AccessTokenUpdated;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Services
use App\Services\ProjectService;

// Resources
use App\Http\Resources\BugResource;
use App\Http\Resources\AccessTokenResource;
use App\Http\Resources\ProjectResource;

// Models
use App\Models\AccessToken;
use App\Models\Project;

// Requests
use App\Http\Requests\AccessTokenStoreRequest;
use App\Http\Requests\AccessTokenUpdateRequest;

/**
 * @OA\Tag(
 *     name="AccessToken",
 * )
 */
class AccessTokenController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/access-tokens",
	 *	tags={"AccessToken"},
	 *	summary="All accessTokens.",
	 *	operationId="allAccessTokenes",
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
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/AccessToken")
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
	public function index(Request $request, Project $project)
	{
		// Check if the user is authorized to list the access tokens of the project
		$this->authorize('viewAny', [AccessToken::class, $project]);

		$accessTokens = $project->accessTokens;

		return AccessTokenResource::collection($accessTokens);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  AccessTokenStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/projects/{project_id}/access-tokens",
	 *	tags={"AccessToken"},
	 *	summary="Store one access token.",
	 *	operationId="storeAccessToken",
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
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The access token description",
	 *                  property="description",
	 *                  type="string",
	 *              )
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/AccessToken"
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
	public function store(AccessTokenStoreRequest $request, Project $project)
	{
		// Check if the user is authorized to create the access token
		$this->authorize('create', [AccessToken::class, $project]);

		// Check if the the request already contains a UUID for the access token
		$id = $this->setId($request);

		// Store the new access token in the database
		$accessToken = new AccessToken();
		$accessToken->id = $id;
		$accessToken->access_token = Str::ulid();
		$accessToken->description = $request->description;
		$accessToken->user_id = $this->user->id;
		$accessToken->project_id = $project->id;

		$accessToken->fireCustomEvent('accessTokenGenerated');
		$accessToken->save();

		return new AccessTokenResource($accessToken);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  AccessToken  $accessToken
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/access-tokens/{access_token_id}",
	 *	tags={"AccessToken"},
	 *	summary="Show one access token.",
	 *	operationId="showAccessToken",
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
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="access_token_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/AccessToken/properties/id"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/AccessToken"
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
	public function show(Project $project, AccessToken $accessToken)
	{
		// Check if the user is authorized to view the access token
		$this->authorize('view', [AccessToken::class, $project]);

		return new AccessTokenResource($accessToken);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  AccessTokenUpdateRequest  $request
	 * @param  AccessToken  $accessToken
	 * @return Response
	 */
	/**
	 * @OA\Patch(
	 *	path="/projects/{project_id}/access-tokens/{access_token_id}",
	 *	tags={"AccessToken"},
	 *	summary="Update a access token.",
	 *	operationId="updateAccessToken",
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
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="access_token_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/AccessToken/properties/id"
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
	 *                  description="The access token description",
	 *                  property="description",
	 *                  type="string",
	 *              )
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/AccessToken"
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
	public function update(AccessTokenUpdateRequest $request, Project $project, AccessToken $accessToken)
	{
		// Check if the user is authorized to update the access token
		$this->authorize('update', [AccessToken::class, $project]);

		// Update the access token
		$accessToken->fill($request->all());

		// Do the save and fire the custom event
		$accessToken->fireCustomEvent('accessTokenUpdated');
		$accessToken->save();

		return new AccessTokenResource($accessToken);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  AccessToken  $accessToken
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/projects/{project_id}/access-tokens/{access_token_id}",
	 *	tags={"AccessToken"},
	 *	summary="Delete a access token.",
	 *	operationId="deleteAccessToken",
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
	 *		name="move",
	 *		required=false,
	 *		in="header"
	 *	),
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="access_token_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/AccessToken/properties/id"
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
	public function destroy(Request $request, Project $project, AccessToken $accessToken)
	{
		// Check if the user is authorized to delete the access token
		$this->authorize('delete', [AccessToken::class, $project]);

		$val = $accessToken->delete();
		$project->fireCustomEvent('projectDeleted');

		return response($val, 204);
	}

	/**
	 * Check url against access token project.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/access-tokens/check-url",
	 *	tags={"AccessToken"},
	 *	summary="Check url against access token.",
	 *	operationId="checkViaAccessToken",
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
	 *		name="access-token",
	 *		required=true,
	 *		in="header"
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
	 *              required={"url","access_token"}
	 *          )
	 *      )
	 *  ),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Project")
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=204,
	 *		description="Url doesn't match the access token project",
	 *	),
	 *
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
	public function checkUrl(Request $request, ProjectService $projectService)
	{
		$accessToken = AccessToken::where('access_token', $request->header('access-token'))->firstOrFail();

		$response = $projectService->checkUrlAgainstProject($accessToken->project, $request->url);

		if ($response == NULL) {
			return response()->json("", 204);
		}

		return new ProjectResource($accessToken->project);
	}

	/**
	 * Check if access token exists.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/access-tokens/validate-access-token",
	 *	tags={"AccessToken"},
	 *	summary="Check if access token exists.",
	 *	operationId="validateAccessToken",
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
	 *		name="access-token",
	 *		required=true,
	 *		in="header"
	 *	),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Project")
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=204,
	 *		description="Url doesn't match the access token project",
	 *	),
	 *
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
	public function validateToken(Request $request)
	{
		$accessToken = AccessToken::where('access_token', $request->header('access-token'))->firstOrFail();

		if (!$accessToken) {
			return response()->json([
				'message' => __('application.access-token-invalid')
			], 404);
		}

		return new ProjectResource($accessToken->project);
	}

	/**
	 * Mark the specified resource as favorite.
	 *
	 * @param  Project  $project
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/access-tokens/{access_token_id}/mark-as-favorite",
	 *	tags={"AccessToken"},
	 *	summary="Mark one access_token as favorite.",
	 *	operationId="markAccessTokenAsFavorite",
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
	 *		name="access_token_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/AccessToken/properties/id"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=false,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="value",
	 *                  type="boolean",
	 *              ),
	 *          )
	 *      )
	 *  ),
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Project"
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
	public function markAsFavorite(Request $request, AccessToken $accessToken)
	{
		// Check if the user is authorized to view the access token
		$this->authorize('view', [AccessToken::class, $accessToken->project]);

		$existingProjectAccessToken = DB::table('project_access_token_users')
			->where('project_id', $accessToken->project->id)
			->where('user_id', Auth::id())
			->first();


		if ($request->has('value') && $request->value === false) {
			DB::table('project_access_token_users')
				->where('project_id', $accessToken->project->id)
				->where('user_id', Auth::id())->delete();

			return new AccessTokenResource($accessToken);
		}

		if (!$existingProjectAccessToken) {
			DB::table('project_access_token_users')->insert([
				'pat_id' => $accessToken->id,
				'project_id' => $accessToken->project->id,
				'user_id' => Auth::id()
			]);
		} else {
			DB::table('project_access_token_users')
				->where('project_id', $accessToken->project->id)
				->where('user_id', Auth::id())->update([
					'pat_id' => $accessToken->id
				]);
		}

		return new AccessTokenResource($accessToken);
	}
}
