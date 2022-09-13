<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
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
		// Company::create([
        //     'id' => 'BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB',
        //     'user_id' => 1, // Testuser John Doe
		// 	'designation' => 'John Doe Company',
        //     'color_hex' => '#ffffff'
		// ]);

		Company::create([
            'id' => 'Feedback-0000-0000-0000-000000000000',
			'designation' => 'BugShot Feedback'
		]);

        // Add Testuser Jane Doe as a teammember
		// User::find(2)->companies()->attach('BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB', ['role_id' => 2]);
	}
}

