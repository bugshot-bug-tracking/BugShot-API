<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\BugResource;
use App\Http\Resources\StatusResource;

// Models
use App\Models\Status;
use App\Models\Project;

// Requests
use App\Http\Requests\StatusStoreRequest;
use App\Http\Requests\StatusUpdateRequest;

/**
 * @OA\Tag(
 *     name="Status",
 * )
 */
class StatusController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/statuses",
	 *	tags={"Status"},
	 *	summary="All statuses.",
	 *	operationId="allStatuses",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
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
	 * 	@OA\Parameter(
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-markers",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachments",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-comments",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachment-base64",
	 *		required=false,
	 *		in="header"
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
	public function index(Request $request, Project $project)
	{
		// Check if the user is authorized to list the statuses of the project
		$this->authorize('viewAny', [Status::class, $project]);

		if($request->timestamp == NULL) {
            $statuses = $project->statuses;
        } else {
            $statuses = $project->statuses->where("statuses.updated_at", ">", date("Y-m-d H:i:s", $request->timestamp));
        }

		return StatusResource::collection($statuses);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  StatusStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/projects/{project_id}/statuses",
	 *	tags={"Status"},
	 *	summary="Store one status.",
	 *	operationId="storeStatus",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
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
	public function store(StatusStoreRequest $request, Project $project)
	{
		// Check if the user is authorized to create the status
		$this->authorize('create', [Status::class, $project]);

		// Check if the the request already contains a UUID for the status
		$id = $this->setId($request);

		// Get the max order number in this project and increase it by one
		$order_number = $project->statuses->max('order_number') + 1;

		// Store the new status in the database
		$status = $project->statuses()->create([
			"id" => $id,
			"designation" => $request->designation,
			"order_number" => $order_number
		]);

		return new StatusResource($status);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Status  $status
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/projects/{project_id}/statuses/{status_id}",
	 *	tags={"Status"},
	 *	summary="Show one status.",
	 *	operationId="showStatus",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
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
	 *		name="status_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Status/properties/id"
	 *		)
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-bugs",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-screenshots",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-markers",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachments",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-comments",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\Parameter(
	 *		name="include-bug-users",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="include-attachment-base64",
	 *		required=false,
	 *		in="header"
	 *	),
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
	public function show(Project $project, Status $status)
	{
		// Check if the user is authorized to view the status
		$this->authorize('view', [Status::class, $project]);

		return new StatusResource($status);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  StatusUpdateRequest  $request
	 * @param  Status  $status
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/projects/{project_id}/statuses/{status_id}",
	 *	tags={"Status"},
	 *	summary="Update a status.",
	 *	operationId="updateStatus",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
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
	public function update(StatusUpdateRequest $request, Project $project, Status $status)
	{
		// Check if the user is authorized to update the status
		$this->authorize('update', [Status::class, $project]);

		// Check if the order of the status has to be synchronized
		if($request->order_number != $status->getOriginal('order_number')) {
			$this->synchronizeStatusOrder($request, $status, $project);
		}

		// Update the status
		$status->update(array_filter([
			"designation" => $request->designation,
			"project_id" => $project->id,
			"order_number" => $request->order_number 
		]));

		return new StatusResource($status);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Status  $status
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/projects/{project_id}/statuses/{status_id}",
	 *	tags={"Status"},
	 *	summary="Delete a status.",
	 *	operationId="deleteStatus",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header"
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
	public function destroy(Project $project, Status $status)
	{
		// Check if the user is authorized to delete the status
		$this->authorize('delete', [Status::class, $project]);

		$val = $status->delete();

		return response($val, 204);
	}

	// Synchronize the order numbers of all the statuses, that are affected by the updated status
	private function synchronizeStatusOrder($request, $status, $project)
	{
		$originalOrderNumber = $status->getOriginal('order_number');
		$newOrderNumber = $request->order_number;

		$statuses = $project->statuses->whereBetween('order_number', [$newOrderNumber, $originalOrderNumber]);
		// Check wether the original or new order_number is bigger because ->whereBetween only works when the first array parameter is smaller than the second
		if($originalOrderNumber < $newOrderNumber) {
			$statuses = $project->statuses->whereBetween('order_number', [$originalOrderNumber, $newOrderNumber]);
		} else {
			$statuses = $project->statuses->whereBetween('order_number', [$newOrderNumber, $originalOrderNumber]);
		}

		// Increase all the order numbers that are greater than the original status order number
		foreach($statuses as $status) {
			$status->update([
				"order_number" => $originalOrderNumber < $newOrderNumber ? $status->order_number - 1 : $status->order_number + 1
			]);
		}
	}
}
