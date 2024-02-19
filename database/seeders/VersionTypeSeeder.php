<?php

namespace Database\Seeders;

use App\Models\VersionType;
use Illuminate\Database\Seeder;

class VersionTypeSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$versionTypes = [
			[
				'designation' => 'security'
			],
			[
				'designation' => 'quality_of_life'
			],
			[
				'designation' => 'bugfixes'
			],
			[
				'designation' => 'general'
			]
		];

		foreach($versionTypes as $versionType)
		{
			if(!VersionType::where('designation', $versionType['designation'])->exists())
			{
				VersionType::create($versionType);
			}
		}
	}
}
