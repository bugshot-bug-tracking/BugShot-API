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
	    VersionType::create([
			'designation' => 'security'
		]);

        VersionType::create([
            'designation' => 'quality_of_life'
		]);

        VersionType::create([
            'designation' => 'bugfixes'
		]);

        VersionType::create([
            'designation' => 'general'
		]);
	}
}
