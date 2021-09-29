<?php

namespace App\Http\Controllers;

use App\Http\Requests\BugRequest;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\BugResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ScreenshotResource;
use App\Models\Bug;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Bug",
 * )
 */
class BugController extends Controller
{

	/**
	 * @OA\Get(
	 *	path="/bug",
	 *	tags={"Bug"},
	 *	summary="All bugs.",
	 *	operationId="allBugs",
	 *	security={ {"sanctum": {} }},
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
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return BugResource::collection(Bug::all());
	}

	/**
	 * @OA\Post(
	 *	path="/bug",
	 *	tags={"Bug"},
	 *	summary="Store one bug.",
	 *	operationId="storeBug",
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
	 *                  description="The bug name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug description",
	 *                  property="description",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug url",
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="priority_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="operating_system",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="browser",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="selector",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="resolution",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="deadline",
	 *                  type="string",
	 * 					format="date-time",
	 *              ),
	 *              required={"project_id","designation","description","url","status_id","priority_id",}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Bug"
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
	 * @param  \Illuminate\Http\BugRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(BugRequest $request)
	{
		$inputs = $request->all();
		$inputs['user_id'] = Auth::id();
		// set the bug status as the first one of the project
		$inputs['status_id'] = Project::find($request->project_id)->statuses()->first()->id;
		$bug = Bug::create($inputs);
		return new BugResource($bug);
	}

	/**
	 * @OA\Get(
	 *	path="/bug/{id}",
	 *	tags={"Bug"},
	 *	summary="Show one bug.",
	 *	operationId="showBug",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Bug"
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
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function show(Bug $bug)
	{
		return new BugResource($bug);
	}

	/**
	 * @OA\Post(
	 *	path="/bug/{id}",
	 *	tags={"Bug"},
	 *	summary="Update a bug.",
	 *	operationId="updateBug",
	 *	security={ {"sanctum": {} }},

	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
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
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="user_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="project_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug name",
	 *                  property="designation",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug description",
	 *                  property="description",
	 *                  type="string",
	 *              ),
	 *              @OA\Property(
	 *                  description="The bug url",
	 *                  property="url",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="status_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="priority_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
	 *  			@OA\Property(
	 *                  property="operating_system",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="browser",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="selector",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="resolution",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="deadline",
	 *                  type="string",
	 * 					format="date-time",
	 *              ),
	 *              required={"user_id","project_id","designation","description","url","status_id","priority_id",}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Bug"
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
	 * @param  \Illuminate\Http\BugRequest  $request
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function update(BugRequest $request, Bug $bug)
	{
		$bug->update($request->all());
		return new BugResource($bug);
	}

	/**
	 * @OA\Delete(
	 *	path="/bug/{id}",
	 *	tags={"Bug"},
	 *	summary="Delete a bug.",
	 *	operationId="deleteBug",
	 *	security={ {"sanctum": {} }},

	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
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
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Bug $bug)
	{
		$val = $bug->delete();
		return response($val, 204);
	}

	/**
	 * @OA\Get(
	 *	path="/bug/{id}/screenshots",
	 *	tags={"Bug"},
	 *	summary="All bug screenshots.",
	 *	operationId="allBugsScreenshots",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Screenshot")
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
	 * Display a list of screenshots that belongs to the bug.
	 *
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function screenshots(Bug $bug)
	{
		$screenshots = ScreenshotResource::collection($bug->screenshots);
		return response()->json($screenshots, 200);
	}

	/**
	 * @OA\Get(
	 *	path="/bug/{id}/attachments",
	 *	tags={"Bug"},
	 *	summary="All bug attachments.",
	 *	operationId="allBugsAttachments",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Attachment")
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
	 * Display a list of attachments that belongs to the bug.
	 *
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function attachments(Bug $bug)
	{
		$attachments = AttachmentResource::collection($bug->attachments);
		return response()->json($attachments, 200);
	}

	/**
	 * @OA\Get(
	 *	path="/bug/{id}/comments",
	 *	tags={"Bug"},
	 *	summary="All bug comments.",
	 *	operationId="allBugsComments",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			type="array",
	 *			@OA\Items(ref="#/components/schemas/Comment")
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
	 * Display a list of comments that belongs to the bug.
	 *
	 * @param  \App\Models\Bug  $bug
	 * @return \Illuminate\Http\Response
	 */
	public function comments(Bug $bug)
	{
		$comments = CommentResource::collection($bug->comments);
		return response()->json($comments, 200);
	}
}
