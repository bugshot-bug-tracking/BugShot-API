<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\OrganizationUserRole;

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
			// "id" => 0,
			"designation" => "Owner"
		]);

		// Only run once
		// $organizations = Organization::all();
		// foreach($organizations as $organization) {
		// 	$user = $organization->creator;
		// 	if($user) {
		// 		$organizationUserRole = OrganizationUserRole::where("user_id", $user->id)->where("organization_id", $organization->id)->where("role_id", 0)->first();
		// 		if(empty($organizationUserRole)) {
		// 			$user->organizations()->attach($organization->id, [
		// 				'role_id' => 0
		// 			]);
		// 		} else if ($organizationUserRole->role_id == 2) {
		// 			$user->organizations()->updateExistingPivot($organizationUserRole->organization_id, [
		// 				'role_id' => 0
		// 			]);
		// 		}
		// 	}
		// }

		Role::create([
			// "id" => 1,
			"designation" => "Manager"
		]);

		Role::create([
			// "id" => 2,
			"designation" => "Team"
		]);

		Role::create([
			// "id" => 3,
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
