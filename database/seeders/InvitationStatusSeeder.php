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
		InvitationStatus::create([
			"id" => 1,
			"designation" => "pending"
		]);

		InvitationStatus::create([
			"id" => 2,
			"designation" => "accepted"
		]);

		InvitationStatus::create([
			"id" => 3,
			"designation" => "declined"
		]);
		InvitationStatus::create([
			"id" => 4,
			"designation" => "expired"
		]);
		InvitationStatus::create([
			"id" => 5,
			"designation" => "duplicate"
		]);
	}
}
