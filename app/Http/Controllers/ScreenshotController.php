<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScreenshotRequest;
use App\Http\Resources\ScreenshotResource;
use App\Models\Bug;
use App\Models\Screenshot;
use Illuminate\Support\Facades\Storage;

class ScreenshotController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return ScreenshotResource::collection(Screenshot::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\ScreenshotRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(ScreenshotRequest $request)
	{
		$storagePath = "/uploads/screenshots";

		$bug = Bug::where("id", $request->bug_id)->with("project")->get()->first();
		$project = $bug->project;
		$company = $project->company;

		$filePath = $storagePath . "/$company->id/$project->id/$bug->id";

		$savedPath = $request->file->store($filePath);

		$screenshot = Screenshot::create([
			"bug_id" => $request->bug_id,
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath,
			"position_x" => $request->position_x,
			"position_y" => $request->position_y,
			"web_position_x" =>  $request->web_position_x,
			"web_position_y" =>  $request->web_position_y,
		]);

		return new ScreenshotResource($screenshot);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Screenshot  $screenshot
	 * @return \Illuminate\Http\Response
	 */
	public function show(Screenshot $screenshot)
	{
		return new ScreenshotResource($screenshot);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\ScreenshotRequest  $request
	 * @param  \App\Models\Screenshot  $screenshot
	 * @return \Illuminate\Http\Response
	 */
	public function update(ScreenshotRequest $request, Screenshot $screenshot)
	{
		$storagePath = "/uploads/screenshots";

		$bug = Bug::where("id", $screenshot->bug_id)->with("project")->get()->first();
		$project = $bug->project;
		$company = $project->company;

		$filePath = $storagePath . "/$company->id/$project->id/$bug->id";

		$savedPath = $request->file->store($filePath);

		Storage::delete($screenshot->url);

		$screenshot->update([
			"designation" => $request->file->getClientOriginalName(),
			"url" => $savedPath,
			"position_x" => $request->position_x,
			"position_y" => $request->position_y,
			"web_position_x" =>  $request->web_position_x,
			"web_position_y" =>  $request->web_position_y,
		]);

		return new ScreenshotResource($screenshot);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Screenshot  $screenshot
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Screenshot $screenshot)
	{
		$val = $screenshot->delete();
		return response($val, 204);
	}

	/**
	 * Download the specified resource.
	 *
	 * @param  \App\Models\Screenshot  $screenshot
	 * @return \Illuminate\Http\Response
	 */
	public function download(Screenshot $screenshot)
	{
		return Storage::download($screenshot->url, $screenshot->designation);
	}
}
