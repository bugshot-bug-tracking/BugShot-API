<?php

namespace App\Http\Controllers;

use App\Http\Resources\CompanyUserRoleResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\ProjectUserRoleResource;
use App\Models\Company;
use App\Models\CompanyUserRole;
use App\Models\Invitation;
use App\Models\Project;
use App\Models\ProjectUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return Invitation::all();
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		return response()->json("", 501);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \Illuminate\Http\Response
	 */
	public function show(Invitation $invitation)
	{
		return new InvitationResource($invitation);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \App\Models\Invitation  $invitation
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, Invitation $invitation)
	{
		return response()->json("", 501);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Invitation  $invitation
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Invitation $invitation)
	{
		$val = $invitation->delete();
		return response($val, 200);
	}


	public function accept(Invitation $invitation)
	{
		if (Auth::id() !== $invitation->target_id)
			return response()->json([
				"errors" => [
					"status" => 403,
					"details" => "This invitation is not for you!"
				]
			], 403);

		if ($invitation->status_id !== 1)
			return response()->json(["data" => [
				"message" => "Invitation already processed."
			]], 288);

		$invitable = $invitation->invitable;

		switch ($invitation->invitable_type) {
			case Company::class:
				return $this->acceptCompany($invitation, $invitable);
				break;

			case Project::class:
				return $this->acceptProject($invitation, $invitable);
				break;
		}

		return response()->json([
			"errors" => [
				"status" => 422,
			]
		], 422);
	}

	public function decline(Invitation $invitation)
	{
		if (Auth::id() !== $invitation->target_id)
			return response()->json([
				"errors" => [
					"status" => 403,
					"details" => "This invitation is not for you!"
				]
			], 403);

		if ($invitation->status_id !== 1)
			return response()->json(["data" => [
				"message" => "Invitation already processed."
			]], 288);

		$invitation->update(["status_id" => 3]);
		return response()->json("", 204);
	}

	private function acceptCompany(Invitation $invitation, Company $company)
	{
		// if a previous invitation was accepted
		$cur = CompanyUserRole::where("company_id", $company->id)->where("user_id", $invitation->target_id)->get();
		if ($cur->count() === 0) {
			$invitation->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => "A previous invitation was already accepted."
			]], 288);
		}

		$companyUserRole = CompanyUserRole::create([
			"company_id" => $company->id,
			"user_id" => $invitation->target_id,
			"role_id" => $invitation->role_id
		]);

		$invitation->update(["status_id" => 2]);

		return new CompanyUserRoleResource($companyUserRole);
	}

	private function acceptProject(Invitation $invitation, Project $project)
	{
		// if a previous invitation was accepted
		$pur = ProjectUserRole::where("project_id", $project->id)->where("user_id", $invitation->target_id)->get();
		if ($pur->count() !== 0) {
			$invitation->update(["status_id" => 5]);
			return response()->json(["data" => [
				"message" => "A previous invitation was already accepted."
			]], 288);
		}

		$projectUserRole = ProjectUserRole::create([
			"project_id" => $project->id,
			"user_id" => $invitation->target_id,
			"role_id" => $invitation->role_id
		]);

		// if user not in company add it to it
		$cur = CompanyUserRole::where("company_id", $project->company->id)->where("user_id", $invitation->target_id)->get();
		if ($cur->count() === 0)
			CompanyUserRole::create([
				"company_id" => $project->company->id,
				"user_id" => $invitation->target_id,
				"role_id" => 7
			]);

		$invitation->update(["status_id" => 2]);

		return new ProjectUserRoleResource($projectUserRole);
	}
}
