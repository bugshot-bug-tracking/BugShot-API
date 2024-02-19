<?php

namespace Database\Seeders;

use App\Models\InvitationStatus;
use Illuminate\Database\Seeder;

class InvitationStatusSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$statuses = [
			[
				"id" => 1,
				"designation" => "pending"
			],
			[
				"id" => 2,
				"designation" => "accepted"
			],
			[
				"id" => 3,
				"designation" => "declined"
			],
			[
				"id" => 4,
				"designation" => "expired"
			],
			[
				"id" => 5,
				"designation" => "duplicate"
			]
		];

		foreach($statuses as $status)
		{
			if(!InvitationStatus::where('id', $status['id'])->exists())
			{
				InvitationStatus::create($status);
			}
		}
	}
}
