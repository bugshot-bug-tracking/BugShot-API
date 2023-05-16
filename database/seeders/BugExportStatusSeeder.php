<?php

namespace Database\Seeders;

use App\Models\BugExportStatus;
use Illuminate\Database\Seeder;

class BugExportStatusSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		BugExportStatus::create([
			"id" => 1,
			"designation" => "pending"
		]);

		BugExportStatus::create([
			"id" => 2,
			"designation" => "approved"
		]);

		BugExportStatus::create([
			"id" => 3,
			"designation" => "declined"
		]);
	}
}
