<?php

namespace Database\Seeders;

use App\Models\Value;
use Illuminate\Database\Seeder;

class ValueSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$values = [
			['designation' => 'az'],
			['designation' => 'za'],
			['designation' => 'newest_first'],
			['designation' => 'oldest_first'],
			['designation' => 'descending'],
			['designation' => 'ascending'],
			['designation' => 'critical_first'],
			['designation' => 'minor_first'],
			['designation' => 'ending_first'],
			['designation' => 'ending_last'],
			['designation' => 'activated'],
			['designation' => 'deactivated'],
			['designation' => 'en'],
			['designation' => 'de'],
			['designation' => 'fr'],
			['designation' => 'ru'],
			['designation' => 'ro'],
			['designation' => 'show_all'],
			['designation' => 'show_basic'],
			['designation' => 'show_custom'],
			['designation' => 'show_custom_show_secondary_view_all_projects_button'], // show_custom sub value
			['designation' => 'show_custom_show_edit_priority_button'], // show_custom sub value
			['designation' => 'show_custom_show_edit_deadline_button'], // show_custom sub value
			['designation' => 'show_custom_show_edit_status_button'], // show_custom sub value
			['designation' => 'receive_notifications_via_app'],
			['designation' => 'receive_notifications_via_mail'],
			['designation' => 'every_notification'],
			['designation' => 'custom_notifications'],
			['designation' => 'custom_notifications_new_bug_added'], // custom_notifications sub value
			['designation' => 'custom_notifications_bug_change_of_status'], // custom_notifications sub value
			['designation' => 'custom_notifications_report_created_deleted'], // custom_notifications sub value
			['designation' => 'custom_notifications_report_finished'], // custom_notifications sub value
			['designation' => 'custom_notifications_assignation_to_client_project_task'], // custom_notifications sub value
			['designation' => 'custom_notifications_new_comments_and_replies'], // custom_notifications sub value
			['designation' => 'custom_notifications_new_tag_in_comment'], // custom_notifications sub value
			['designation' => 'light_mode'],
			['designation' => 'dark_mode'],
			['designation' => 'system']
		];

		Value::insert($values);
	}
}
