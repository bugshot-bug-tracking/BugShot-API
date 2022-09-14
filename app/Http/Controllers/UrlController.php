<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\Relation;

// Resources
use App\Http\Resources\UrlResource;

// Models
use App\Models\Project;
use App\Models\Url;

// Requests
use App\Http\Requests\UrlStoreRequest;
use App\Http\Requests\UrlUpdateRequest;

/**
 * @OA\Tag(
 *     name="Url",
 * )
 */
class UrlController extends Controller
{

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/{type}/{id}/urls",
	 *	tags={"Url"},
	 *	summary="All urls of the given resource.",
	 *	operationId="allUrls",
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
	 *			@OA\Items(ref="#/components/schemas/Url")
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
		// Check if the user is authorized to list the urls of the given project
		$this->authorize('viewAny', $project);

		return UrlResource::collection($project->urls);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  ProjectStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/{type}/{id}/urls",
	 *	tags={"Url"},
	 *	summary="Store one url.",
	 *	operationId="storeUrl",
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
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The url",
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="Send true if the url is supposed to be the primary url for this project. Else send false",
	 *                  property="primary",
	 *                  type="string",
	 *              ),
	 *              required={"url","primary"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Url"
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
	public function store(UrlStoreRequest $request, $type, $id)
	{
		// Check if the given type is a user or an organization
		$class = Relation::getMorphedModel($type);
		if($class == Project::class) {
			$model = Project::find($id);
			// Check if the user is authorized to store a url for the given project
			$this->authorize('createUrl', [Project::class]);
		} else {
			//
		}
		
        // Create the url
        $url = $model->urls()->create([
            "id" => (string) Str::uuid(),
			"url" => substr($request->url, -1) == '/' ? substr($request->url, 0, -1) : $request->url, // Check if the given url has "/" as last char and if so, store url without it,
			"primary" => $request->primary
		]);

		return new UrlResource($url);
	}

	/**
	 * Display the specified resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/{type}/{id}/urls/{url_id}",
	 *	tags={"Url"},
	 *	summary="Show one url.",
	 *	operationId="showUrl",
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
	 *	@OA\Parameter(
	 *		name="url_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Url/properties/id"
	 *		)
	 *	),
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
	public function show(Project $project, Url $projectUrl)
	{
		// Check if the user is authorized to view the url
		$this->authorize('view', $project);

		return new UrlResource($projectUrl);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  UrlUpdateRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/{type}/{id}/urls/{url_id}",
	 *	tags={"Url"},
	 *	summary="Update a url.",
	 *	operationId="updateUrl",
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
	 *	@OA\Parameter(
	 *		name="url_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Url/properties/id"
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
	 *                  description="The url",
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="Send true if the url is supposed to be the primary url for this project. Else send false",
	 *                  property="primary",
	 *                  type="string",
	 *              ),
	 *              required={"url","primary"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Url"
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
	public function update(UrlUpdateRequest $request, Project $project, Url $projectUrl)
	{
		// Check if the user is authorized to update the url
		$this->authorize('update', $project);

		// Update the project
		$projectUrl->update($request->all());
		$projectUrl->update([
            "url" => substr($request->url, -1) == '/' ? substr($request->url, 0, -1) : $request->url // Check if the given url has "/" as last char and if so, store url without it
		]);

		return new UrlResource($projectUrl);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/{type}/{id}/urls/{url_id}",
	 *	tags={"Url"},
	 *	summary="Delete a url.",
	 *	operationId="deleteUrl",
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
	 *		name="url_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Url/properties/id"
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
	public function destroy(Project $project, Url $projectUrl)
	{
		// Check if the user is authorized to delete the url
		$this->authorize('delete', $project);

		// Softdelete the url
		$val = $projectUrl->delete();

		return response($val, 204);
	}
}
