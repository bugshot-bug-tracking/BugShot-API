<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProjectSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Project::create([
            'id' => 'CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC',
            'user_id' => 1, // Testuser John Doe
            'company_id' => 'BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB',
			'designation' => 'Awesome project',
            'color_hex' => '#ffffff',
            'url' => 'https://www.mydealz.de'
		]);

        // Add Testuser Jane Doe as a teammember
		User::find(2)->projects()->attach('CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC', ['role_id' => 2]);
	}
}

