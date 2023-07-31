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

class RenameInitialOrganizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'organizations:rename';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the name of the inital organization of a user, if they have not changed it themselfs yet';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Only execute once for the existing live data
        $users = User::all();
        foreach ($users as $user) {

			$user->organizations()
				->where('designation', 'like', '%My Organization (%')
				->update([
					'designation' => $user->first_name . " " . $user->last_name . "'s " . Str::title(__('data.organization', [], GetUserLocaleService::getLocale($user)))
				]);

			$user->organizations()
				->where('designation', 'like', '%Meine Organisation (%')
				->update([
					'designation' => $user->first_name . " " . $user->last_name . "'s " . Str::title(__('data.organization', [], GetUserLocaleService::getLocale($user)))
				]);
        }
    }
}
