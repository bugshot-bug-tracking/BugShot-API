<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyUserRole;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\SettingUserValue;
use App\Models\Value;
use App\Services\GetUserLocaleService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecreateAllUserSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usersettings:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all user settings and set them freshly';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		DB::transaction(function () {
			$userIds = SettingUserValue::groupBy("user_id")->pluck("user_id");
			SettingUserValue::query()->delete();

			foreach($userIds as $userId) {
				$user = User::find($userId);

				foreach(Setting::all() as $setting) {
					$defaultValue = Value::where("designation", $setting->default_value)->first();
					$defaultValueId = $defaultValue ? $defaultValue->id : NULL;

					$user->settings()->attach([
						$setting->id => ['value_id' => $defaultValueId]
					]);
				}
			}
		});
    }
}
