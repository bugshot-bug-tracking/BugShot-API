<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachmentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use App\Models\Bug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
	 * @param  \Illuminate\Http\AttachmentRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(AttachmentRequest $request)
	{
		$storagePath = "/uploads/attachments";

		$bug = Bug::where("id", $request->bug_id)->with("project")->get()->first();
		$project = $bug->project;
		$company = $project->company;

		$filePath = $storagePath . "/$company->id/$project->id/$bug->id";

		$savedPath = $request->file->store($filePath);

		$attachment = Attachment::create([
			"bug_id" => $request->bug_id,
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath,
		]);

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
	 * @param  \Illuminate\Http\AttachmentRequest  $request
	 * @param  \App\Models\Attachment  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function update(AttachmentRequest $request, Attachment $attachment)
	{
		$storagePath = "/uploads/attachments";

		$bug = Bug::where("id", $attachment->bug_id)->with("project")->get()->first();
		$project = $bug->project;
		$company = $project->company;

		$filePath = $storagePath . "/$company->id/$project->id/$bug->id";

		$savedPath = $request->file->store($filePath);

		Storage::delete($attachment->url);

		$attachment->update([
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath,
		]);

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

	/**
	 * Download the specified resource.
	 *
	 * @param  \App\Models\Attachment  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function download(Attachment $attachment)
	{
		return Storage::download($attachment->url, $attachment->designation);
	}
}
