<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		// \App\Models\User::factory(10)->create();

        // Adds versiontypes
		$this->call(VersionTypeSeeder::class);

        // Adds clients
		$this->call(ClientSeeder::class);

        // Adds versions
		$this->call(VersionSeeder::class);

		// // for bug priorities
		$this->call(PrioritySeeder::class);

		// // for company/project user roles
		$this->call(RoleSeeder::class);

		// // for invitation statuses like pending or accepted
		$this->call(InvitationStatusSeeder::class);

        // // Adds a default user
		$this->call(UserSeeder::class);

        // // Adds a default organization
		$this->call(OrganizationSeeder::class);

        // // Adds a default company
		$this->call(CompanySeeder::class);

        // // Adds a default project
		$this->call(ProjectSeeder::class);

        // // Adds a default project
		$this->call(StatusSeeder::class);

        // // Adds a default bug
		$this->call(BugSeeder::class);
	}
}
