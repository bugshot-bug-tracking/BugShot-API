<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return AttachmentResource::collection(Attachment::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(AttachmentRequest $request)
	{
		$attachment = Attachment::create($request->all());
		return new AttachmentResource($attachment);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Attachment  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function show(Attachment $attachment)
	{
		return new AttachmentResource($attachment);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Attachment  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function update(AttachmentRequest $request, Attachment $attachment)
	{
		$attachment->update($request->all());
		return new AttachmentResource($attachment);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Attachment  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Attachment $attachment)
	{
		$val = $attachment->delete();
		return response($val, 204);
	}
}
