<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * @OA\Tag(
 *     name="Auth",
 * )
 */
class AuthController extends Controller
{

	/**
	 * @OA\Post(
	 *	path="/auth/register",
	 *	tags={"Auth"},
	 *	summary="Register a user.",
	 *	operationId="authRegister",
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="first_name",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="last_name",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="email",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="password",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="password_confirmation",
	 *                  type="string",
	 *              ),
	 *              required={"first_name","last_name","email","password","password_confirmation"}
	 *          )
	 *      )
	 *  ),

	 *	@OA\Response(
	 *		response=201,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/User"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *)
	 *
	 **/
	public function register(Request $request)
	{
		$fields = $request->validate([
			"first_name" => ["required", "alpha_dash", "max:255"],
			"last_name" => ["required", "alpha_dash", "max:255"],
			"email" => ["required", "email", "unique:users,email"],
			"password" => ["required", "confirmed", Password::min(8)->letters()->numbers()],
			'password_confirmation' => ["required", "same:password"],

		]);

		$user = User::create([
			"first_name" => $fields["first_name"],
			"last_name" => $fields["last_name"],
			"email" => $fields["email"],
			"password" => Hash::make($fields["password"]),
		]);

		return new UserResource($user);
	}

	/**
	 * @OA\Post(
	 *	path="/auth/login",
	 *	tags={"Auth"},
	 *	summary="Log in.",
	 *	operationId="authLogin",
	 *
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="email",
	 *                  type="string",
	 *              ),
	 *  			@OA\Property(
	 *                  property="password",
	 *                  type="string",
	 *              ),
	 *              required={"email","password"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/User"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *)
	 *
	 **/
	public function login(Request $request)
	{
		$fields = $request->validate([
			"email" => ["required", "email"],
			"password" => ["required"],
			// "client_id" => ["required", "integer"]
		]);

		$user = User::where("email", $fields["email"])->first();

		if (!$user || !Hash::check($fields["password"], $user->password))
			return response()->json(["message" => "Bad Credentials!"], 401);

		// ? Set the token name to either device name or device type in the future
		$token = $user->createToken("mytoken");

		return response()->json([
			"data" => [
				"user" => new UserResource($user),
				"token" => $token->plainTextToken
			]
		], 200);
	}


	/**
	 * @OA\Post(
	 *	path="/auth/logout",
	 *	tags={"Auth"},
	 *	summary="Log out.",
	 *	operationId="authLogout",
	 *
	 *	@OA\Response(
	 *		response=204,
	 *		description="Success",
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *)
	 *
	 **/
	public function logout(Request $request)
	{
		$request->user()->currentAccessToken()->delete();
		return response()->json("", 204);
	}

	/**
	 * @OA\Post(
	 *	path="/auth/user",
	 *	tags={"Auth"},
	 *	summary="Show current user.",
	 *	operationId="authUser",
	 *	security={ {"sanctum": {} }},
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success",
	 *		@OA\JsonContent(
	 *			ref="#/components/schemas/User"
	 *		)
	 *	),
	 *	@OA\Response(
	 *		response=400,
	 *		description="Bad Request"
	 *	),
	 *	@OA\Response(
	 *		response=401,
	 *		description="Unauthenticated"
	 *	),
	 *	@OA\Response(
	 *		response=403,
	 *		description="Forbidden"
	 *	),
	 *	@OA\Response(
	 *		response=404,
	 *		description="Not Found"
	 *	),
	 *)
	 *
	 **/
	public function user()
	{
		return new UserResource(Auth::user());
	}
}
