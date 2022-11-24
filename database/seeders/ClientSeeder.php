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
			'designation' => 'zapier interface',
			'client_url' => 'https://dev-interface.view4all.de/api/zapier',
			'client_key' => '28k?cX>pab3q2P9<m_ekq5<A.c{Kn$',
		]);
	}
}
