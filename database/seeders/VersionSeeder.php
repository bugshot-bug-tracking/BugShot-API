<?php

namespace Database\Seeders;

use App\Models\Version;
use Illuminate\Database\Seeder;

class VersionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
        //webpanel
	    Version::create([
            'client_id' => 1,
            'version_type_id' => 4,
			'designation' => '1.0.0',
            'description' => 'Initial Version',
            'supported' => 1
		]);

        //desktop
        Version::create([
            'client_id' => 2,
            'version_type_id' => 4,
			'designation' => '1.0.0',
            'description' => 'Initial Version',
            'supported' => 1
		]);

        //app_ios
        Version::create([
            'client_id' => 3,
            'version_type_id' => 4,
			'designation' => '1.0.0',
            'description' => 'Initial Version',
            'supported' => 1
		]);

        //app_android
        Version::create([
            'client_id' => 4,
            'version_type_id' => 4,
			'designation' => '1.0.0',
            'description' => 'Initial Version',
            'supported' => 1
		]);

        //browserext_chrome
        Version::create([
            'client_id' => 5,
            'version_type_id' => 4,
			'designation' => '1.0.0',
            'description' => 'Initial Version',
            'supported' => 1
		]);

        //mac_os
        Version::create([
            'client_id' => 6,
            'version_type_id' => 4,
			'designation' => '1.0.0',
            'description' => 'Initial Version',
            'supported' => 1
		]);

        //ios_share_extension
        Version::create([
            'client_id' => 7,
            'version_type_id' => 4,
			'designation' => '1.0.0',
            'description' => 'Initial Version',
            'supported' => 1
		]);

        //flyer_creator
        Version::create([
            'client_id' => 8,
            'version_type_id' => 4,
			'designation' => '1.0.0',
            'description' => 'Initial Version',
            'supported' => 1
		]);
        
        //Interface
        Version::create([
            'client_id' => 9,
            'version_type_id' => 4,
			'designation' => '1.0.0',
            'description' => 'Initial Version',
            'supported' => 1
		]);

	}
}