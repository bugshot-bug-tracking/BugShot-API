<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\Relation;

// Resources
use App\Http\Resources\ApiTokenResource;

// Models
use App\Models\Project;
use App\Models\ApiToken;

// Requests
use App\Http\Requests\ApiTokenStoreRequest;
use App\Http\Requests\ApiTokenUpdateRequest;

/**
 * @OA\Tag(
 *     name="ApiToken",
 * )
 */
class ApiTokenController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @param Request $request
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/{type}/{id}/api-tokens",
	 *	tags={"ApiToken"},
	 *	summary="All api-tokens of the given resource.",
	 *	operationId="allApiTokens",
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
	 *		name="type",
	 *		required=true,
	 *		in="path"
	 *	),
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path"
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/ApiToken")
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
	public function index(Request $request, $type, $id)
	{
		// Check if the user is authorized to list the urls of the given model
		$class = Relation::getMorphedModel($type);
		if($class == Project::class) {
			$model = Project::find($id);
			// Check if the user is authorized to store a url for the given project
		} else {
			//
		}

		$this->authorize('viewAnyApiTokens', $model);

		return ApiTokenResource::collection($model->apiTokens);
	}

	/**
	 * @OA\Post(
	 *	path="/{type}/{id}/api-tokens",
	 *	tags={"ApiToken"},
	 *	summary="Creates the api token for a given resource",
	 *	operationId="storeApiToken",
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
	 *		name="type",
	 *		required=true,
	 *		in="path"
	 *	),
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path"
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="If the user wants to create a custom token, he can send it here.",
	 *                  property="token",
	 *					type="string"
	 *              )
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
	public function store(ApiTokenStoreRequest $request, $type, $id)
	{
		// Check if the user is authorized to create an api token for this resource
		$class = Relation::getMorphedModel($type);
		if($class == Project::class) {
			$model = Project::find($id);
		} else {
			//
		}

		$this->authorize('createApiToken', $model);

		// Check if the resource already has an api token
		if($model->apiToken != NULL) {
			return response()->json(["data" => [
				"message" => __('application.api-token-already-exists')
			]], 409);
		}

		// Create the api token
		$apiToken = $model->apiTokens()->create([
            "token" => $request->token == '' ? (string) Str::uuid() : $request->token
        ]);

		return new ApiTokenResource($apiToken);
	}

	/**
	 * @OA\Put(
	 *	path="/{type}/{id}/api-tokens/{api_token_id}",
	 *	tags={"ApiToken"},
	 *	summary="Update an api token for a given resource",
	 *	operationId="updateApiToken",
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
	 *		name="type",
	 *		required=true,
	 *		in="path"
	 *	),
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path"
	 *	),
	 *	@OA\Parameter(
	 *		name="api_token_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/ApiToken/properties/id"
	 *		)
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="If the user wants to create a custom token, he can send it here.",
	 *                  property="token",
	 *					type="string"
	 *              )
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
	public function update(ApiTokenStoreRequest $request, $type, $id, ApiToken $apiToken)
	{
		// Check if the user is authorized to create an api token for this resource
		$class = Relation::getMorphedModel($type);
		if($class == Project::class) {
			$model = Project::find($id);
		} else {
			//
		}

		$this->authorize('updateApiToken', $model);

        // Update the api token
		$apiToken->update([
            "token" => $request->token == '' ? (string) Str::uuid() : $request->token
        ]);

		return new ApiTokenResource($apiToken);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/{type}/{id}/api-tokens/{api_token_id}",
	 *	tags={"ApiToken"},
	 *	summary="Delete an api token of a given resource",
	 *	operationId="deleteApiToken",
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
	 *		name="type",
	 *		required=true,
	 *		in="path"
	 *	),
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path"
	 *	),
	 *	@OA\Parameter(
	 *		name="api_token_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/ApiToken/properties/id"
	 *		)
	 *	),
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
	public function destroy($type, $id, ApiToken $apiToken)
	{
		// Check if the user is authorized to delete the url
		$class = Relation::getMorphedModel($type);
		if($class == Project::class) {
			$model = Project::find($id);
		} else {
			//
		}

		$this->authorize('deleteApiToken', $model);

		// Softdelete the url
		$val = $apiToken->delete();

		return response($val, 204);
	}
}
