<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Setting::create([
			"designation" => "company_filter_alphabetical"
		]);

		Setting::create([
			"designation" => "company_filter_creation"
		]);

		Setting::create([
			"designation" => "company_filter_last_updated"
		]);

		Setting::create([
			"designation" => "project_filter_alphabetical"
		]);

		Setting::create([
			"designation" => "project_filter_creation"
		]);

		Setting::create([
			"designation" => "project_filter_last_updated"
		]);

        Setting::create([
			"designation" => "bug_filter_alphabetical"
		]);

		Setting::create([
			"designation" => "bug_filter_creation"
		]);

		Setting::create([
			"designation" => "bug_filter_priority"
		]);

        Setting::create([
			"designation" => "bug_filter_deadline"
		]);

        Setting::create([
			"designation" => "bug_filter_assigned_to"
		]);

        Setting::create([
			"designation" => "user_settings_interface_language"
		]);

        Setting::create([
			"designation" => "user_settings_show_ui_elements"
		]);

        Setting::create([
			"designation" => "user_settings_receive_mail_notifications"
		]);

        Setting::create([
			"designation" => "user_settings_select_notifications"
		]);

        Setting::create([
			"designation" => "user_settings_darkmode"
		]);

		// Added 21.08.2023
        Setting::create([
			"designation" => "custom_notifications_new_bug_added"
		]);

		Setting::create([
			"designation" => "custom_notifications_bug_change_of_status"
		]);

		Setting::create([
			"designation" => "custom_notifications_report_created"
		]);

		Setting::create([
			"designation" => "custom_notifications_report_finished"
		]);

		Setting::create([
			"designation" => "custom_notifications_assignation_to_client_project_task"
		]);

		Setting::create([
			"designation" => "custom_notifications_new_comments_and_replies"
		]);

		Setting::create([
			"designation" => "custom_notifications_new_tag_in_comment"
		]);

		Setting::create([
			"designation" => "show_custom_show_secondary_view_all_projects_button"
		]);

		Setting::create([
			"designation" => "show_custom_show_edit_priority_button"
		]);

		Setting::create([
			"designation" => "show_custom_show_edit_deadline_button"
		]);

		Setting::create([
			"designation" => "show_custom_show_edit_status_button"
		]);

		Setting::create([
			"designation" => "custom_notifications_daily_summary"
		]);

		Setting::create([
			"designation" => "custom_notifications_invitation_received"
		]);

		Setting::create([
			"designation" => "custom_notifications_tagged_in_comment"
		]);

		// Added 28.08.2023
		Setting::create([
			"designation" => "custom_notifications_assigned_to_bug"
		]);

		// Added 21.09.2023
		Setting::create([
			"designation" => "custom_notifications_implementation_approval_form_received"
		]);
	}
}
