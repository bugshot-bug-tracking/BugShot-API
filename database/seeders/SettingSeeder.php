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
		$settings = [
			[
				"designation" => "user_settings_interface_language",
				"default_value" => "en"
			],
			[
				"designation" => "user_settings_select_notifications",
				"default_value" => "every_notification"
			],
			[
				"designation" => "custom_notifications_tagged_in_comment",
				"default_value" => "activated"
			],
			[
				"designation" => "custom_notifications_assigned_to_bug",
				"default_value" => "activated"
			],
			[
				"designation" => "custom_notifications_daily_summary",
				"default_value" => "activated"
			],
			[
				"designation" => "custom_notifications_invitation_received",
				"default_value" => "activated"
			],
			[
				"designation" => "custom_notifications_implementation_approval_form_received",
				"default_value" => "activated"
			],
			[
				"designation" => "custom_notifications_report_created",
				"default_value" => "activated"
			],
			[
				"designation" => "tour_status",
				"default_value" => NULL
			],
			[
				"designation" => "user_settings_app_notifications",
				"default_value" => "activated"
			],
			[
				"designation" => "user_settings_mail_select_notifications",
				"default_value" => "activated"
			],
			[
				"designation" => "beta_user",
				"default_value" => "deactivated"
			],
		];

		foreach($settings as $setting)
		{
			if(!Setting::where('designation', $setting['designation'])->exists())
			{
				Setting::create($setting);
			}
		}
	}
}
