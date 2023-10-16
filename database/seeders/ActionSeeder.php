<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Action;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$actions = [ // TODO: For now lets only do project actions
			// Organizations
			"organization_created",
			"organization_updated",
			"organization_deleted",

			// Groups
			"group_created",
			"group_updated",
			"group_deleted",

			// Projects
			"project_created",
			"project_updated",
			"project_deleted",
			"project_moved_to_new_group",
			"project_access_token_generated",
			"project_access_token_deleted",

			// Bugs
			"bug_created",
			"bug_updated",
			"bug_deleted",
			"bugs_archived",
			"bug_status_changed",
			"bugs_moved_to_new_project",

			// Users
			"user_assigned_to_bug",
			"user_assigment_to_bug_revoked",
			"user_invited",
			"user_role_updated",
			"user_removed",

			// TODO: Keep finding new actions and check if an history entry is made for a organization when e.g. a bug is moved to a new status
		];

		foreach($actions as $action) {
			Action::create([
				"designation" => $action
			]);
		}
    }
}
