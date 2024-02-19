<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationUserRole;
use App\Models\User;
use App\Models\Role;
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
		$organizations = [
			[
				'id' => 'AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA',
				'user_id' => 1, // Testuser John Doe
				'designation' => 'John Doe Organization'
			]
		];

		foreach($organizations as $organization)
		{
			if(!Organization::where('id', $organization['id'])->withTrashed()->exists())
			{
				Organization::create($organization);
			}
		}

		if(!OrganizationUserRole::where('organization_id', 'AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA')->where('user_id', '2')->exists())
		{
        	// Add Testuser Jane Doe as a manager
			User::find(2)->organizations()->attach('AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA', ['role_id' => Role::TEAM]);
		}
	}
}

