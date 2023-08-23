<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;
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

			foreach($users as $user) {
				$settingIds = array(17, 18, 19, 20, 21, 22, 23, 24, 25);

				foreach($settingIds as $settingId) {
					$settingUserValue = SettingUserValue::where("user_id", $user->id)->where("setting_id", $settingId)->first();

					if($settingUserValue == NULL) {
						$user->settings()->attach([$settingId => ['value_id' => 42]]);
					}
				}
			}
		});
    }
}
