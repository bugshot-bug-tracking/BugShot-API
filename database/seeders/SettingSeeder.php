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
			"id" => 1,
			"designation" => "company_filter_alphabetical"
		]);

		Setting::create([
			"id" => 2,
			"designation" => "company_filter_creation"
		]);

		Setting::create([
			"id" => 3,
			"designation" => "company_filter_last_updated"
		]);

		Setting::create([
			"id" => 4,
			"designation" => "project_filter_alphabetical"
		]);

		Setting::create([
			"id" => 5,
			"designation" => "project_filter_creation"
		]);

		Setting::create([
			"id" => 6,
			"designation" => "project_filter_last_updated"
		]);

        Setting::create([
			"id" => 7,
			"designation" => "bug_filter_alphabetical"
		]);

		Setting::create([
			"id" => 8,
			"designation" => "bug_filter_creation"
		]);

		Setting::create([
			"id" => 9,
			"designation" => "bug_filter_priority"
		]);

        Setting::create([
			"id" => 10,
			"designation" => "bug_filter_deadline"
		]);

        Setting::create([
			"id" => 11,
			"designation" => "bug_filter_assigned_to"
		]);

        Setting::create([
			"id" => 12,
			"designation" => "user_settings_interface_language"
		]);

        Setting::create([
			"id" => 13,
			"designation" => "user_settings_show_ui_elements"
		]);

        Setting::create([
			"id" => 14,
			"designation" => "user_settings_receive_mail_notifications"
		]);

        Setting::create([
			"id" => 15,
			"designation" => "user_settings_select_notifications"
		]);

        Setting::create([
			"id" => 16,
			"designation" => "user_settings_darkmode"
		]);
	}
}
