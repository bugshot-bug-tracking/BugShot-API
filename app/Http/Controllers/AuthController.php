<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
	public function register(Request $request)
	{
		$fields = $request->validate([
			"first_name" => ["required", "alpha_dash", "max:255"],
			"last_name" => ["required", "alpha_dash", "max:255"],
			"email" => ["required", "email", "unique:users,email"],
			"password" => ["required", "confirmed", Password::min(8)->letters()->numbers()]
		]);

		$user = User::create([
			"first_name" => $fields["first_name"],
			"last_name" => $fields["last_name"],
			"email" => $fields["email"],
			"password" => bcrypt($fields["password"]),
		]);

		return new UserResource($user);
	}

	public function login(Request $request)
	{
		$fields = $request->validate([
			"email" => ["required", "email"],
			"password" => ["required"]
		]);

		$user = User::where("email", $fields["email"])->first();

		if (!$user || !Hash::check($fields["password"], $user->password))
			return response()->json(["message" => "Bad Credentials!"], 401);

		$token = $user->createToken("mytoken");

		return response()->json([
			"data" => [
				"user" => new UserResource($user),
				"token" => $token->plainTextToken
			]
		], 200);
	}

	public function logout(Request $request)
	{
		$request->user()->currentAccessToken()->delete();
		return response()->json("", 204);
	}

	public function user()
	{
		return new UserResource(Auth::user());
	}
}
