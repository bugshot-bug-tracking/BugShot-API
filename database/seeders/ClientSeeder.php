<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ClientSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
	    Client::create([
			'designation' => 'webpanel'
		]);

        Client::create([
			'designation' => 'desktop'
		]);

        Client::create([
			'designation' => 'app_ios'
		]);

        Client::create([
			'designation' => 'app_android'
		]);

        Client::create([
			'designation' => 'browserext_chrome'
		]);

        Client::create([
			'designation' => 'mac_os'
		]);

        Client::create([
			'designation' => 'ios_share_extension'
		]);

        Client::create([
			'designation' => 'flyer_creator'
		]);

		Client::create([
			'designation' => 'interface'
		]);
	}
}
