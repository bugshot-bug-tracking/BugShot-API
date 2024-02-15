<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;
use App\Models\Setting;
use App\Models\Value;
use App\Models\CompanyUserRole;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Models\SettingUserValue;
use App\Services\GetUserLocaleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddNewSettingsAndValuesToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usersettings:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add newly added settings to the users with default values';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		DB::transaction(function () {

			$users = User::all();
			$settings = Setting::all();

			foreach($users as $user)
			{
				$settingUserValues = SettingUserValue::where("user_id", $user->id)->get();
				foreach($settings as $setting)
				{
					if(!$settingUserValues->contains('setting_id', $setting->id))
					{
						$defaultValue = Value::where('designation', $setting->default_value)->first();
						$user->settings()->attach([$setting->id => ['value_id' => $defaultValue->id]]);
					}
				}
			}
		});
    }
}
