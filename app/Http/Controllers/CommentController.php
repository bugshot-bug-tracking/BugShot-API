<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\CommentResource;

// Services
use App\Services\CommentService;
use App\Services\ApiCallService;

// Models
use App\Models\Comment;
use App\Models\Bug;
use App\Models\User;

// Requests
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;

// Events
use App\Events\CommentSent;
use App\Events\TaggedInComment;

/**
 * @OA\Tag(
 *     name="Comment",
 * )
 */
class CommentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/comments",
	 *	tags={"Comment"},
	 *	summary="All comments.",
	 *	operationId="allComments",
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
	 * 	@OA\Parameter(
	 *		name="bug_id",
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
	public function index(Bug $bug)
	{
		// Check if the user is authorized to list the comments of the bug
		$this->authorize('viewAny', [Comment::class, $bug->project]);

		return CommentResource::collection($bug->comments);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/interface/bugs/{bug_id}/comments",
	 *	tags={"Comment"},
	 *	summary="All comments.",
	 *	operationId="allCommentsViaApiKey",
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="api-key",
	 *		required=true,
	 *		in="header",
	 * 		example="d1359f79-ce2d-45b1-8fd8-9566c606aa6c"
	 *	),
	 *
	 * 	@OA\Parameter(
	 *		name="bug_id",
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
	public function indexViaApiKey(Request $request, Bug $bug)
	{
		//Check if user has access to bug
		$tempProject = $request->get('project');
		if($bug->project_id == $tempProject->id){
			return CommentResource::collection($bug->comments);
		}
		$response = [
            'success' => false,
            'message' => 'The bug was not found or is not available to the user!',
        ];

        return response()->json($response, 404);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  CommentStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/comments",
	 *	tags={"Comment"},
	 *	summary="Store one comment.",
	 *	operationId="storeComment",
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
	 *	 @OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The message",
	 *                  property="content",
	 *                  type="string",
	 *              ),
	 *   			@OA\Property(
	 *                  property="tagged",
	 *                  type="array",
	 * 					@OA\Items(
	 * 	   					@OA\Property(
	 *              		    property="user_id",
	 *              		    type="string"
	 *              		)
	 * 					)
	 *              ),
	 *              required={"bug_id","content"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Comment"
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
	public function store(CommentStoreRequest $request, Bug $bug, CommentService $commentService, ApiCallService $apiCallService)
	{
		// Check if the user is authorized to create the comment
		$this->authorize('create', [Comment::class, $bug->project]);

		$client_id = $request->get('client_id');
		return $commentService->store($request,$bug, Auth::id(), $this, $client_id, $apiCallService);
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  CommentStoreRequest  $request
	 * @return Response
	 */
	/**
	 * @OA\Post(
	 *	path="/interface/bugs/{bug_id}/comments",
	 *	tags={"Comment"},
	 *	summary="Store one comment.",
	 *	operationId="storeCommentViaApiKey",
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="api-key",
	 *		required=true,
	 *		in="header",
	 * 		example="d1359f79-ce2d-45b1-8fd8-9566c606aa6c"
	 *	),
	 *
	 *	 @OA\Parameter(
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *              @OA\Property(
	 *                  description="The message",
	 *                  property="content",
	 *                  type="string",
	 *              ),
	 *   			@OA\Property(
	 *                  property="tagged",
	 *                  type="array",
	 * 					@OA\Items(
	 * 	   					@OA\Property(
	 *              		    property="user_id",
	 *              		    type="string"
	 *              		)
	 * 					)
	 *              ),
	 *              required={"bug_id","content"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Comment"
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
	public function storeViaApiKey(CommentStoreRequest $request, Bug $bug, CommentService $commentService, ApiCallService $apiCallService)
	{
		// Get user information if a api key was used
		$tempProject = $request->get('project');
		$creator_id = $tempProject->user_id;

		$client_id = $request->get('client_id');
		return $commentService->store($request,$bug, $creator_id, $this, $client_id, $apiCallService);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Comment  $comment
	 * @return Response
	 */
	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/comments/{comment_id}",
	 *	tags={"Comment"},
	 *	summary="Show one comment.",
	 *	operationId="showComment",
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
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="comment_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Comment/properties/id"
	 *		)
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Comment"
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
	public function show(Bug $bug, Comment $comment)
	{
		// Check if the user is authorized to view the comment
		$this->authorize('view', [Comment::class, $bug->project]);

		return new CommentResource($comment);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  CommentUpdateRequest  $request
	 * @param  Comment  $comment
	 * @return Response
	 */
	/**
	 * @OA\Put(
	 *	path="/bugs/{bug_id}/comments/{comment_id}",
	 *	tags={"Comment"},
	 *	summary="Update a comment.",
	 *	operationId="updateComment",
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
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="comment_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Comment/properties/id"
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
	 *                  property="content",
	 *                  type="string",
	 *              ),
	 *              required={"bug_id","user_id","content"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/Comment"
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
	public function update(CommentUpdateRequest $request, Bug $bug, Comment $comment)
	{
		// Check if the user is authorized to update the comment
		$this->authorize('update', [$comment, $bug->project]);

		$comment->update($request->all());

		return new CommentResource($comment);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  Comment  $comment
	 * @return Response
	 */
	/**
	 * @OA\Delete(
	 *	path="/bugs/{bug_id}/comments/{comment_id}",
	 *	tags={"Comment"},
	 *	summary="Delete a comment.",
	 *	operationId="deleteComment",
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
	 *		name="bug_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Bug/properties/id"
	 *		)
	 *	),
	 *	@OA\Parameter(
	 *		name="comment_id",
	 *		required=true,
	 *		in="path",
	 *		@OA\Schema(
	 *			ref="#/components/schemas/Comment/properties/id"
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
	public function destroy(Bug $bug, Comment $comment, CommentService $commentService)
	{
		// Check if the user is authorized to delete the comment
		$this->authorize('update', [$comment, $comment->bug->project]);

		$val = $commentService->delete($comment);

		return response($val, 204);
	}
}
