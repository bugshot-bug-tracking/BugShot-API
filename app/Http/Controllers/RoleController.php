<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return RoleResource::collection(Role::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(RoleRequest $request)
	{
		$role = Role::create($request->all());
		return new RoleResource($role);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Role  $role
	 * @return \Illuminate\Http\Response
	 */
	public function show(Role $role)
	{
		return new RoleResource($role);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Role  $role
	 * @return \Illuminate\Http\Response
	 */
	public function update(RoleRequest $request, Role $role)
	{
		$role->update($request->all());
		return new RoleResource($role);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Role  $role
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Role $role)
	{
		$val = $role->delete();
		return response($val, 204);
	}
}
