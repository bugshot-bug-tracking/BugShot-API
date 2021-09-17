<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return CommentResource::collection(Comment::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(CommentRequest $request)
	{
		$comment = Comment::create($request->all());
		return new CommentResource($comment);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Comment  $comment
	 * @return \Illuminate\Http\Response
	 */
	public function show(Comment $comment)
	{
		return new CommentResource($comment);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Comment  $comment
	 * @return \Illuminate\Http\Response
	 */
	public function update(CommentRequest $request, Comment $comment)
	{
		$comment->update($request->all());
		return new CommentResource($comment);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Comment  $comment
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Comment $comment)
	{
		$val = $comment->delete();
		return response($val, 204);
	}
}
