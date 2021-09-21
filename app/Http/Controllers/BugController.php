<?php

namespace App\Http\Controllers;

use App\Http\Requests\BugRequest;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\BugResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ScreenshotResource;
use App\Models\Bug;
use Illuminate\Http\Request;

class BugController extends Controller
{
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
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\BugRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(BugRequest $request)
	{
		$bug = Bug::create($request->all());
		return new BugResource($bug);
	}

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
