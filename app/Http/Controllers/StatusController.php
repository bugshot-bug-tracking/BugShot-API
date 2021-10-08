<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusRequest;
use App\Http\Resources\BugResource;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Status",
 * )
 */
class StatusController extends Controller
{
	/**
	 * @OA\Get(
	 *	path="/status",
	 *	tags={"Status"},
	 *	summary="All statuses.",
	 *	operationId="allStatuses",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Status")
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
		return StatusResource::collection(Status::all());
	}

	/**
	 * @OA\Post(
	 *	path="/status",
	 *	tags={"Status"},
	 *	summary="Store one status.",
	 *	operationId="storeStatus",
	 *	security={ {"sanctum": {} }},
	 *
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="project_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *              @OA\Property(
	 *                  description="The message",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              required={"project_id","designation"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Status"
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
	 * @param  \Illuminate\Http\StatusRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(StatusRequest $request)
	{
		$status = Status::create($request->all());
		return new StatusResource($status);
	}

	/**
	 * @OA\Get(
	 *	path="/status/{id}",
	 *	tags={"Status"},
	 *	summary="Show one status.",
	 *	operationId="showStatus",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Status"
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
	 * @param  \App\Models\Status  $status
	 * @return \Illuminate\Http\Response
	 */
	public function show(Status $status)
	{
		return new StatusResource($status);
	}

	/**
	 * @OA\Post(
	 *	path="/status/{id}",
	 *	tags={"Status"},
	 *	summary="Update a status.",
	 *	operationId="updateStatus",
	 *	security={ {"sanctum": {} }},

	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
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
	 *                  property="project_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *              @OA\Property(
	 *                  description="The message",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              required={"project_id","designation"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Status"
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
	 * @param  \Illuminate\Http\StatusRequest  $request
	 * @param  \App\Models\Status  $status
	 * @return \Illuminate\Http\Response
	 */
	public function update(StatusRequest $request, Status $status)
	{
		$status->update($request->all());
		return new StatusResource($status);
	}

	/**
	 * @OA\Delete(
	 *	path="/status/{id}",
	 *	tags={"Status"},
	 *	summary="Delete a status.",
	 *	operationId="deleteStatus",
	 *	security={ {"sanctum": {} }},

	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
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
	 * @param  \App\Models\Status  $status
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Status $status)
	{
		$val = $status->delete();
		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/status/{id}/bugs",
	 *	tags={"Status"},
	 *	summary="All status bugs.",
	 *	operationId="allStatusesBugs",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Bug")
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
	 * Display a list of bugs that belongs to the respective status.
	 *
	 * @param  \App\Models\Status  $status
	 * @return \Illuminate\Http\Response
	 */
	public function bugs(Status $status)
	{
		$bugs = BugResource::collection($status->bugs);
		return response()->json($bugs, 200);
	}
}
