<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectUserRole;
use App\Models\Role;
use App\Models\User;
use App\Models\Status;
use Illuminate\Support\Str;
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
		$projects = [
			[
				'id' => 'CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC',
				'user_id' => 1, // Testuser John Doe
				'company_id' => 'BBBBBBBB-BBBB-BBBB-BBBB-BBBBBBBBBBBB',
				'designation' => 'Awesome project',
				'color_hex' => '#ffffff',
				'url' => 'https://www.mydealz.de'
			],
			[
				'id' => 'Feedback-0000-0000-0000-000000000000',
				'designation' => 'BugShot Feedback',
				'company_id' => 'Feedback-0000-0000-0000-000000000000'
			]
		];

		foreach($projects as $project)
		{
			if(!Project::where('id', $project['id'])->exists())
			{
				Project::create($project);
			}
		}

		// Create the default statuses for the new project
		$defaultStatuses = [__('data.backlog'), __('data.todo'), __('data.doing'), __('data.done')];

		foreach ($defaultStatuses as $key => $status) {
			if(!Status::where('designation', $status)->withTrashed()->exists())
			{
				Status::create([
					"id" => $status == 'Backlog' ? "Backlog0-0000-0000-0000-000000000000" : (string) Str::uuid(),
					"designation" => $status,
					"order_number" => $key++,
					"project_id" => "Feedback-0000-0000-0000-000000000000",
					"permanent" => $key == 1 || $key == 4 ? ($key == 1 ? 'backlog' : 'done') : NULL // Check wether the status is backlog or done
				]);
			}
		}

		if(!ProjectUserRole::where('project_id', 'CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC')->where('user_id', '2')->exists())
		{
        	// Add Testuser Jane Doe as a manager
			User::find(2)->projects()->attach('CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC', ['role_id' => Role::TEAM]);
		}
	}
}

