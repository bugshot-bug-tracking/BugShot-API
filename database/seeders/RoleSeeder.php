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
		if(!Role::where('id', Role::OWNER)->exists())
		{
			Role::create([
				// "id" => 0,
				"designation" => "Owner"
			]);

			Role::first()->update([
				"id" => 0
			]);
		}

		$roles = [
			[
				"id" => 1,
				"designation" => "Manager"
			],
			[
				"id" => 2,
				"designation" => "Team"
			],
			[
				"id" => 3,
				"designation" => "Client"
			]
		];

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

		foreach($roles as $role)
		{
			if(!Role::where('id', $role['id'])->exists())
			{
				Role::create($role);
			}
		}
	}
}
