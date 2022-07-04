<?php

namespace Database\Seeders;

use App\Models\Value;
use Illuminate\Database\Seeder;

class ValueSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$values = [
			['designation' => 'az'],
			['designation' => 'za'],
			['designation' => 'newest_first'],
			['designation' => 'oldest_first'],
			['designation' => 'descending'],
			['designation' => 'ascending'],
			['designation' => 'critical_first'],
			['designation' => 'minor_first'],
			['designation' => 'ending_first'],
			['designation' => 'ending_last'],
			['designation' => 'activated'],
			['designation' => 'deactivated'],
			['designation' => 'en'],
			['designation' => 'de'],
			['designation' => 'fr'],
			['designation' => 'ru'],
			['designation' => 'ro'],
			['designation' => 'show_all'],
			['designation' => 'show_basic'],
			['designation' => 'show_custom'],
		];

		Value::insert($values);
	}
}
