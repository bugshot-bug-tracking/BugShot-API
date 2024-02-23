<?php

namespace Database\Seeders;

use App\Models\Bug;
use App\Models\BugUserRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BugSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
	    $bugs = [
			[
				'id' => 'EEEEEEEE-EEEE-EEEE-EEEE-EEEEEEEEEEEE',
				'user_id' => 1,
				'project_id' => 'CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC',
				'designation' => 'Bad bug',
				'description' => 'Example description',
				'url' => 'https://www.mydealz.de/gutscheine',
				'status_id' => 'DDDDDDDD-DDDD-DDDD-DDDD-DDDDDDDDDDD4',
				'priority_id' => 3,
				'operating_system' => 'Windows',
				'browser' => 'Chrome 99.99',
				'selector' => 'div',
				'resolution' => '1920x1080',
				'order_number' => 1,
				'ai_id' => 1,
				'client_id' => 1,
				'deadline' => Carbon::now()
			]
		];

		foreach($bugs as $bug)
		{
			if(!Bug::where('id', $bug['id'])->withTrashed()->exists())
			{
				Bug::create($bug);
			}
		}

		if(!BugUserRole::where('bug_id', 'EEEEEEEE-EEEE-EEEE-EEEE-EEEEEEEEEEEE')->where('user_id', '2')->exists())
		{
			// Assign Testuser Jane Doe as a teammember
			User::find(2)->bugs()->attach('EEEEEEEE-EEEE-EEEE-EEEE-EEEEEEEEEEEE', ['role_id' => Role::TEAM]);
		}
	}
}
