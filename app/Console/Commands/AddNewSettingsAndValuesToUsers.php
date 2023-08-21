<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyUserRole;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Services\GetUserLocaleService;
use Illuminate\Support\Facades\DB;

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
				$userSettings = $user->settings();

				if(!$userSettings->wherePivot("setting_id", 17)->exists()) {
					$user->settings()->attach([17 => ['value_id' => 42]]);
				}

				if(!$userSettings->wherePivot("setting_id", 18)->exists()) {
					$user->settings()->attach([18 => ['value_id' => 42]]);
				}

				if(!$userSettings->wherePivot("setting_id", 19)->exists()) {
					$user->settings()->attach([19 => ['value_id' => 42]]);
				}

				if(!$userSettings->wherePivot("setting_id", 20)->exists()) {
					$user->settings()->attach([20 => ['value_id' => 42]]);
				}

				if(!$userSettings->wherePivot("setting_id", 21)->exists()) {
					$user->settings()->attach([21 => ['value_id' => 42]]);
				}

				if(!$userSettings->wherePivot("setting_id", 22)->exists()) {
					$user->settings()->attach([22 => ['value_id' => 42]]);
				}

				if(!$userSettings->wherePivot("setting_id", 23)->exists()) {
					$user->settings()->attach([23 => ['value_id' => 42]]);
				}

				if(!$userSettings->wherePivot("setting_id", 24)->exists()) {
					$user->settings()->attach([24 => ['value_id' => 42]]);
				}

				if(!$userSettings->wherePivot("setting_id", 25)->exists()) {
					$user->settings()->attach([25 => ['value_id' => 42]]);
				}
			}
		});
    }
}
