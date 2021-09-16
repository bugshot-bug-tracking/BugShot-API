<?php

namespace App\Http\Controllers;

use App\Http\Requests\BugRequest;
use App\Http\Resources\BugResource;
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
	 * @param  \Illuminate\Http\Request  $request
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
	 * @param  \Illuminate\Http\Request  $request
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
}
