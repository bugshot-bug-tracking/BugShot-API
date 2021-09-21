<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyUserRoleResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUserRoleResource;
use App\Models\Company;
use App\Models\CompanyUserRole;
use App\Models\Project;
use App\Models\ProjectUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

	/**
	 * Display all companies whom the user is affiliated with
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function companies()
	{
		return CompanyUserRoleResource::collection(
			CompanyUserRole::where("user_id", Auth::user()->id)
				->with('company')
				->with('user')
				->with("role")
				->get()
		);

		return response()->json("", 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function companyProjects(Company $company)
	{
		$projects = ProjectUserRoleResource::collection(
			ProjectUserRole::where([
				["user_id", Auth::user()->id]
			])
				->with(
					'project',
					function ($query) use ($company) {
						$query->where('company_id', $company->id);
					}
				)
				->with('user')
				->with("role")
				->get()
		)->whereNotNull('project');

		return response()->json($projects, 200);
	}

	// public function checkProject(Request $request)
	// {
	// 	//
	// }
}
