<?php

namespace App\Services;

use App\Events\CommentSent;
use App\Events\TaggedInComment;
use App\Http\Controllers\CommentController;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use App\Models\Bug;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use App\Models\Comment;
use App\Models\User;

class CommentService
{
	// Delete the comment
	public function delete($comment)
	{
		$val = $comment->delete();

		return $val;
	}

	public function store(CommentStoreRequest $request, Bug $bug, $user_id, CommentController $commentController, $client_id)
	{
		// Check if the the request already contains a UUID for the comment
		$id = $commentController->setId($request);

		preg_match(
			'/(?<=@)[\p{L}\p{N}]+/',
			$request->content,
			$matches
		);

		// Store the new comment in the database
		$comment = $bug->comments()->create([
			'id' => $id,
			'content' => $request->content,
			'user_id' => $user_id,
			"client_id" => $client_id
		]);

		// Notify the tagged users
		foreach ($request->tagged as $tagged) {
			$user = User::find($tagged['user_id']);
			$user ? TaggedInComment::dispatch($user, $comment) : true;
		}

		// Broadcast the event
		broadcast(new CommentSent($commentController->user, $comment, $request->tagged))->toOthers();

		return triggerInterfaces(new CommentResource($comment), 5, $bug->project->id);
	}

}
