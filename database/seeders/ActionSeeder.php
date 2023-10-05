<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Action;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$actions = [
			"created",
			"updated",
			"deleted",
			"moved_to_new_organization",
			"moved_to_new_group",
			"moved_to_new_project",
			// TODO: Keep finding new actions and check if an history entry is made for a organization when e.g. a bug is moved to a new status
		];

		Action::insert([
			[
				"designation" => "created",
			],
		]);
    }
}
