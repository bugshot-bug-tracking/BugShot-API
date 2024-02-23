<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Models\CompanyUserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$companies = [
			[
				'id' => 'BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB',
				'user_id' => 1, // Testuser John Doe
				'organization_id' => 'AAAAAAAA-AAAA-AAAA-AAAA-AAAAAAAAAAAA',
				'designation' => 'John Doe Company',
				'color_hex' => '#ffffff'
			],
			[
				'id' => 'Feedback-0000-0000-0000-000000000000',
				'designation' => 'BugShot Feedback'
			]
		];

		foreach($companies as $company)
		{
			if(!Company::where('id', $company['id'])->withTrashed()->exists())
			{
				Company::create($company);
			}
		}

		if(!CompanyUserRole::where('company_id', 'BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB')->where('user_id', '2')->exists())
		{
			// Add Testuser Jane Doe as a teammember
			User::find(2)->companies()->attach('BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB', ['role_id' => Role::TEAM]);
		}
	}
}

