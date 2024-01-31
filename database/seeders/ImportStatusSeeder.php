<?php

namespace Database\Seeders;

use App\Models\ImportStatus;
use Illuminate\Database\Seeder;

class ImportStatusSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$statuses = [
			['id' => 1, 'designation' => 'pending'],
			['id' => 2, 'designation' => 'imported'],
			['id' => 3, 'designation' => 'import_failed']
		];

		ImportStatus::insert($statuses);
	}
}
