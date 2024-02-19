<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$priorities = [
			[
				"id" => 1,
				"designation" => "Minor"
			],
			[
				"id" => 2,
				"designation" => "Normal"
			],
			[
				"id" => 3,
				"designation" => "Important"
			],
			[
				"id" => 4,
				"designation" => "Critical"
			]
		];

		foreach($priorities as $priority)
		{
			if(!Priority::where('id', $priority['id'])->exists())
			{
				Priority::create($priority);
			}
		}
	}
}
