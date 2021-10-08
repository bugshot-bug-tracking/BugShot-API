<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyUserRoleResource;
use App\Http\Resources\ProjectResource;
use App\Models\Company;
use App\Models\CompanyUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return CompanyResource::collection(Company::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\CompanyRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(CompanyRequest $request)
	{
		$company = Company::create($request->all());

		$companyUserRole = CompanyUserRole::create([
			"company_id" => $company->id,
			"user_id" => Auth::id(),
			"role_id" => 1 // Owner
		]);

		return new CompanyUserRoleResource($companyUserRole);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function show(Company $company)
	{
		return new CompanyResource($company);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\CompanyRequest  $request
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function update(CompanyRequest $request, Company $company)
	{
		$company->update($request->all());
		return new CompanyResource($company);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Company $company)
	{
		$val = $company->delete();
		return response($val, 204);
	}

	/**
	 * Display a list of projects that belongs to the company.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function projects(Company $company)
	{
		$projects = ProjectResource::collection($company->projects);
		return response()->json($projects, 200);
	}

	/**
	 * Display a list of users that belongs to the company.
	 *
	 * @param  \App\Models\Company  $company
	 * @return \Illuminate\Http\Response
	 */
	public function users(Company $company)
	{
		$company_user_roles = CompanyUserRoleResource::collection(
			CompanyUserRole::where("company_id", $company->id)
				->with('company')
				->with('user')
				->with("role")
				->get()
		);
		return response()->json($company_user_roles, 200);
	}
}
