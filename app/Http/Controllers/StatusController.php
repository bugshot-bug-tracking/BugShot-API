<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusRequest;
use App\Http\Resources\BugResource;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Status",
 * )
 */
class StatusController extends Controller
{
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/statuses",
	 *	tags={"Status"},
	 *	summary="All statuses.",
	 *	operationId="allStatuses",
	 *	security={ {"sanctum": {} }},
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
	public function index(Request $request, Project $project)
	{
		if($request->timestamp == NULL) {
            $statuses = $project->statuses->where("project_id", $project->id);
        } else {
            $statuses = $project->statuses->where([
				["project_id", "=", $project->id],
                ["statuses.updated_at", ">", date("Y-m-d H:i:s", $request->timestamp)]
			]);
        }

		return StatusResource::collection($statuses);
	}

	/**
	 * @OA\Post(
	 *	path="/projects/{project_id}/statuses",
	 *	tags={"Status"},
	 *	summary="Store one status.",
	 *	operationId="storeStatus",
	 *	security={ {"sanctum": {} }},
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The message",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 * 	 	  		@OA\Property(
	 *                  property="order_number",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *              required={"designation", "order_number"}
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
	public function store(StatusRequest $request, Project $project)
	{
		// Check if the the request already contains a UUID for the status
        if($request->id == NULL) {
            $id = (string) Str::uuid();
        } else {
            $id = $request->id;
        }

		// Store the new status in the database
		$status = Status::create([
			"id" => $id,
			"designation" => $request->designation,
			"project_id" => $project->id,
			"order_number" => $request->order_number 
		]);

		return new StatusResource($status);
	}

	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/statuses/{status_id}",
	 *	tags={"Status"},
	 *	summary="Show one status.",
	 *	operationId="showStatus",
	 *	security={ {"sanctum": {} }},
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
	 *		name="status_id",
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
	public function show(Project $project, Status $status)
	{
		return new StatusResource($status);
	}

	/**
	 * @OA\Put(
	 *	path="/projects/{project_id}/statuses/{status_id}",
	 *	tags={"Status"},
	 *	summary="Update a status.",
	 *	operationId="updateStatus",
	 *	security={ {"sanctum": {} }},
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="status_id",
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
	 *              @OA\Property(
	 *                  description="The message",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 * 	 	  		@OA\Property(
	 *                  property="order_number",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *              required={"designation", "order_number"}
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
	public function update(StatusRequest $request, Project $project, Status $status)
	{
		$status->update([
			"designation" => $request->designation,
			"project_id" => $project->id,
			"order_number" => $request->order_number 
		]);

		return new StatusResource($status);
	}

	/**
	 * @OA\Delete(
	 *	path="/projects/{project_id}/statuses/{status_id}",
	 *	tags={"Status"},
	 *	summary="Delete a status.",
	 *	operationId="deleteStatus",
	 *	security={ {"sanctum": {} }},
	 *	@OA\Parameter(
	 *		name="project_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Project/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="status_id",
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
		$val = $status->update([
			"deleted_at" => new \DateTime()
		]);

		return response($val, 204);
	}
}
