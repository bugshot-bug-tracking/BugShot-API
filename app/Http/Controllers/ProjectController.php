<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\BugResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectUserRoleResource;
use App\Http\Resources\StatusResource;
use App\Models\Project;
use App\Models\ProjectUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return ProjectResource::collection(Project::all());
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\ProjectRequest  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(ProjectRequest $request)
	{
		$project = Project::create($request->all());

		$projectUserRole = ProjectUserRole::create([
			"project_id" => $project->id,
			"user_id" => Auth::id(),
			"role_id" => 1 // Owner
		]);

		return new ProjectUserRoleResource($projectUserRole);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function show(Project $project)
	{
		return new ProjectResource($project);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\ProjectRequest  $request
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function update(ProjectRequest $request, Project $project)
	{
		$project->update($request->all());
		return new ProjectResource($project);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Project $project)
	{
		$val = $project->delete();
		return response($val, 204);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function statuses(Project $project)
	{
		$statuses = StatusResource::collection($project->statuses);

		return response()->json($statuses, 200);
	}

	/**
	 * Display a list of bugs that belongs to the project.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function bugs(Project $project)
	{
		$bugs = BugResource::collection($project->bugs);
		return response()->json($bugs, 200);
	}

	/**
	 * Display a list of users that belongs to the project.
	 *
	 * @param  \App\Models\Project  $project
	 * @return \Illuminate\Http\Response
	 */
	public function users(Project $project)
	{
		$project_user_roles = ProjectUserRoleResource::collection(
			ProjectUserRole::where("project_id", $project->id)
				->with('project')
				->with('user')
				->with("role")
				->get()
		);

		return response()->json($project_user_roles, 200);
	}
}
