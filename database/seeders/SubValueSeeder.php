<?php

namespace Database\Seeders;

use App\Models\SubValue;
use Illuminate\Database\Seeder;

class SubValueSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$subValues = [
			['designation' => 'show_secondary_view_all_projects_button'],
			['designation' => 'show_edit_priority_button'],
			['designation' => 'show_edit_deadline_button'],
			['designation' => 'show_edit_status_button'],
			['designation' => 'new_bug_added'],
			['designation' => 'bug_change_of_status'],
			['designation' => 'report_created_deleted'],
			['designation' => 'report_finished'],
			['designation' => 'assignation_to_client_project_task'],
			['designation' => 'new_comments_and_replies'],
			['designation' => 'new_tag_in_comment']
		];

		SubValue::insert($subValues);
	}
}
