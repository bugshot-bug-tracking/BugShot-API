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
		Priority::create([
			"id" => 1,
			"designation" => "Minor"
		]);

		Priority::create([
			"id" => 2,
			"designation" => "Normal"
		]);

		Priority::create([
			"id" => 3,
			"designation" => "Important"
		]);
		Priority::create([
			"id" => 4,
			"designation" => "Critical"
		]);
	}
}
