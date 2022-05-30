<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Organization::create([
            'id' => 'AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA',
            'user_id' => 1, // Testuser John Doe
			'designation' => 'John Doe Organization'
		]);

        // Add Testuser Jane Doe as a manager
		User::find(2)->organizations()->attach('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', ['role_id' => 1]);
	}
}

