<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\CommentResource;

// Services
use App\Services\CommentService;

// Models
use App\Models\Comment;
use App\Models\Bug;

// Requests
use App\Http\Requests\CommentRequest;

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
	 * @return \Illuminate\Http\Response
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
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
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
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\CommentRequest  $request
	 * @return \Illuminate\Http\Response
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
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
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
	public function store(CommentRequest $request, Bug $bug)
	{
		// Check if the user is authorized to create the comment
		$this->authorize('create', [Comment::class, $bug->project]);

		// Check if the the request already contains a UUID for the comment
		$id = $this->setId($request);

		// Store the new comment in the database
		$comment = $bug->comments()->create([
			'id' => $id,
			'content' => $request->content,
			'user_id' => Auth::id()
		]);

		return new CommentResource($comment);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Comment  $comment
	 * @return \Illuminate\Http\Response
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
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
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
	 * @param  \Illuminate\Http\CommentRequest  $request
	 * @param  \App\Models\Comment  $comment
	 * @return \Illuminate\Http\Response
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
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
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
	public function update(CommentRequest $request, Bug $bug, Comment $comment)
	{
		// Check if the user is authorized to update the comment
		$this->authorize('update', [$comment, $bug->project]);

		$comment->update($request->all());

		return new CommentResource($comment);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Comment  $comment
	 * @return \Illuminate\Http\Response
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
	 *		in="header"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
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
