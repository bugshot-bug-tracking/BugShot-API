<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriorityRequest;
use App\Http\Resources\PriorityResource;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return PriorityResource::collection(Priority::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(PriorityRequest $request)
	{
		$priority = Priority::create($request->all());
		return new PriorityResource($priority);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Priority  $priority
	 * @return \Illuminate\Http\Response
	 */
	public function show(Priority $priority)
	{
		return new PriorityResource($priority);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Priority  $priority
	 * @return \Illuminate\Http\Response
	 */
	public function update(PriorityRequest $request, Priority $priority)
	{
		$priority->update($request->all());
		return new PriorityResource($priority);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Priority  $priority
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Priority $priority)
	{
		$val = $priority->delete();
		return response($val, 204);
	}
}
