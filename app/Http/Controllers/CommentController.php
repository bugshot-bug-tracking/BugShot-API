<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Resources
use App\Http\Resources\CommentResource;

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
	 * @OA\get(
	 *	path="/bugs/{bug_id}/comments",
	 *	tags={"Comment"},
	 *	summary="All comments.",
	 *	operationId="allComments",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Bug $bug)
	{
		return CommentResource::collection($bug->comments);
	}

	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/comments",
	 *	tags={"Comment"},
	 *	summary="Store one comment.",
	 *	operationId="storeComment",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\CommentRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(CommentRequest $request, Bug $bug)
	{
		// Check if the the request already contains a UUID for the comment
        if($request->id == NULL) {
            $id = (string) Str::uuid();
        } else {
            $id = $request->id;
        }

		// Store the new comment in the database
		$comment = $bug->comments()->create([
			'id' => $id,
			'content' => $request->content,
			'user_id' => Auth::id()
		]);

		return new CommentResource($comment);
	}

	/**
	 * @OA\Get(
	 *	path="/bugs/{bug_id}/comments/{comment_id}",
	 *	tags={"Comment"},
	 *	summary="Show one comment.",
	 *	operationId="showComment",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Comment  $comment
	 * @return \Illuminate\Http\Response
	 */
	public function show(Bug $bug, Comment $comment)
	{
		return new CommentResource($comment);
	}

	/**
	 * @OA\Post(
	 *	path="/bugs/{bug_id}/comments/{comment_id}",
	 *	tags={"Comment"},
	 *	summary="Update a comment.",
	 *	operationId="updateComment",
	 *	security={ {"sanctum": {} }},
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
	 *  			@OA\Property(
	 *                  property="bug_id",
	 * 					type="string",
	 *  				maxLength=255,
	 *              ),
	 *  			@OA\Property(
	 *                  property="user_id",
	 *                  type="integer",
	 *                  format="int64",
	 *              ),
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
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\CommentRequest  $request
	 * @param  \App\Models\Comment  $comment
	 * @return \Illuminate\Http\Response
	 */
	public function update(CommentRequest $request, Bug $bug, Comment $comment)
	{
		$comment->update($request->all());

		return new CommentResource($comment);
	}

	/**
	 * @OA\Delete(
	 *	path="/bugs/{bug_id}/comments/{comment_id}",
	 *	tags={"Comment"},
	 *	summary="Delete a comment.",
	 *	operationId="deleteComment",
	 *	security={ {"sanctum": {} }},
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
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Comment  $comment
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Bug $bug, Comment $comment)
	{
		$val = $comment->update([
			"deleted_at" => new \DateTime()
		]);

		return response($val, 204);
	}
}
