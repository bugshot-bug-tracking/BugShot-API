<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Role::create([
			"id" => 0,
			"designation" => "Owner"
		]);

		Role::create([
			"id" => 1,
			"designation" => "Manager"
		]);

		Role::create([
			"id" => 2,
			"designation" => "Team"
		]);

		Role::create([
			"id" => 3,
			"designation" => "Client"
		]);

		// Role::create([
		// 	"id" => 6,
		// 	"designation" => "Client"
		// ]);

		// Role::create([
		// 	"id" => 7,
		// 	"designation" => "Visitor"
		// ]);
	}
}
