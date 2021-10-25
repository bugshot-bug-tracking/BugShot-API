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

		// for bug priorities
		$this->call(PrioritySeeder::class);

		// for company/project user roles
		$this->call(RoleSeeder::class);

		// for invitation statuses like pending or accepted
		$this->call(InvitationStatusSeeder::class);
	}
}
