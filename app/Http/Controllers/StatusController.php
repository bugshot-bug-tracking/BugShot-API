<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusRequest;
use App\Http\Resources\BugResource;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return StatusResource::collection(Status::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\StatusRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(StatusRequest $request)
	{
		$status = Status::create($request->all());
		return new StatusResource($status);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Status  $status
	 * @return \Illuminate\Http\Response
	 */
	public function show(Status $status)
	{
		return new StatusResource($status);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\StatusRequest  $request
	 * @param  \App\Models\Status  $status
	 * @return \Illuminate\Http\Response
	 */
	public function update(StatusRequest $request, Status $status)
	{
		$status->update($request->all());
		return new StatusResource($status);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Status  $status
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Status $status)
	{
		$val = $status->delete();
		return response($val, 204);
	}

	/**
	 * Display a list of bugs that belongs to the respective status.
	 *
	 * @param  \App\Models\Status  $status
	 * @return \Illuminate\Http\Response
	 */
	public function bugs(Status $status)
	{
		$bugs = BugResource::collection($status->bugs);
		return response()->json($bugs, 200);
	}
}
