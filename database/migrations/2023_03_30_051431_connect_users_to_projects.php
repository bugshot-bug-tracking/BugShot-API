<?php

use App\Models\ProjectUserRole;
use App\Models\Project;
use App\Models\OrganizationUserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only execute once for the existing live data
        $projects = Project::all();

		foreach($projects as $project) {
			$projectUserRole = ProjectUserRole::where("project_id", $project->id)->where("user_id", $project->user_id)->first();

			if(!$projectUserRole) {
				if($project->creator) {
					$project->creator->projects()->attach($project->id, ['role_id' => 0]);
				}
			}
		}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
