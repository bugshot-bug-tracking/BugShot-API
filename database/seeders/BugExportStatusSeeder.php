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
		$bugExports = [
			[
				"id" => 1,
				"designation" => "pending"
			],
			[
				"id" => 2,
				"designation" => "approved"
			],
			[
				"id" => 3,
				"designation" => "declined"
			]
		];

		foreach($bugExports as $bugExport)
		{
			if(!BugExportStatus::where('id', $bugExport['id'])->exists())
			{
				BugExportStatus::create($bugExport);
			}
		}
	}
}
