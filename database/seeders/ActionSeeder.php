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
		$actions = [
			// Organizations (TODO: For now lets only do project actions)
			// "organization_name_changed",
			// "organization_deleted",
			// "organization_group_moved_to_new_organization",

			// Projects
			"project_name_changed",
			"project_moved_to_new_group",
			"project_bug_status_changed",
			"project_bug_deleted",
			"project_bug_created",
			"project_bug_data_updated",
			"proejct_user_assigned_to_bug",
			"proejct_user_assigment_to_bug_revoked",
			"project_user_invited",
			"project_bugs_moved_to_new_project",
			"project_access_token_generated",
			"project_access_token_deleted"
			// TODO: Keep finding new actions and check if an history entry is made for a organization when e.g. a bug is moved to a new status
		];

		foreach($actions as $action) {
			Action::create([
				"designation" => $action
			]);
		}
    }
}
