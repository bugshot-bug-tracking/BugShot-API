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
		$clients = [
			[
				'designation' => 'webpanel'
			],
			[
				'designation' => 'desktop'
			],
			[
				'designation' => 'app_ios'
			],
			[
				'designation' => 'app_android'
			],
			[
				'designation' => 'browserext_chrome'
			],
			[
				'designation' => 'mac_os'
			],
			[
				'designation' => 'ios_share_extension'
			],
			[
				'designation' => 'flyer_creator'
			],
			[
				'designation' => 'zapier interface',
				'client_url' => 'https://dev-interface-zapier.bugshot.de/api/zapier',
				'client_key' => '28k?cX>pab3q2P9<m_ekq5<A.c{Kn$',
			],
			[
					'designation' => 'atlassian integration',
			]
			];

		foreach($clients as $client)
		{
			if(!Client::where('designation', $client['designation'])->exists())
			{
				Client::create($client);
			}
		}
	}
}
