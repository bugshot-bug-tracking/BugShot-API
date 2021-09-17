<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScreenshotRequest;
use App\Http\Resources\ScreenshotResource;
use App\Models\Screenshot;
use Illuminate\Http\Request;

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
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(ScreenshotRequest $request)
	{
		$screenshot = Screenshot::create($request->all());
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
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Screenshot  $screenshot
	 * @return \Illuminate\Http\Response
	 */
	public function update(ScreenshotRequest $request, Screenshot $screenshot)
	{
		$screenshot->update($request->all());
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
}
