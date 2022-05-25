<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		User::create([
			'first_name' => 'John',
			'last_name' => 'Doe',
			'email' => 'john@mail.de',
			'email_verified_at' => Carbon::now(),
			'password' => Hash::make('password1'),
			'is_admin' => true
		]);

        User::create([
			'first_name' => 'Jane',
			'last_name' => 'Doe',
			'email' => 'jane@mail.de',
			'email_verified_at' => Carbon::now(),
			'password' => Hash::make('password1'),
			'is_admin' => false
		]);
	}
}
