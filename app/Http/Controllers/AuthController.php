<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordFacade;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

// Notifications
use App\Notifications\VerifyEmailAddressNotification;
use App\Notifications\VerificationSuccessfulNotification;
use App\Notifications\PasswordResetSuccessfulNotification;

// Resources
use App\Http\Resources\UserResource;
use App\Http\Resources\SettingUserValueResource;

// Services
use App\Services\SendinblueService;
use App\Services\GetUserLocaleService;

// Models
use App\Models\User;
use App\Models\SettingUserValue;
use App\Models\Setting;

// Requests
use App\Http\Requests\CustomEmailVerificationRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Notifications\UserRegisteredNotification;
use App\Http\Requests\CheckEmailRequest;
use App\Models\Value;

/**
 * @OA\Tag(
 *     name="Auth",
 * )
 */
class AuthController extends Controller
{
	/**
	 * @OA\Post(
	 *	path="/auth/check-email",
	 *	tags={"Auth"},
	 *	summary="Check if the given email address is already in use.",
	 *	operationId="checkEmail",
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="email",
	 *                  type="string",
	 *              ),
	 *              required={"email"}
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
	public function checkIfMailAlreadyExists(CheckEmailRequest $request)
	{
		return response()->json("Email address is valid", 200);
	}

	/**
	 * @OA\Post(
	 *	path="/auth/register",
	 *	tags={"Auth"},
	 *	summary="Register a user.",
	 *	operationId="authRegister",
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
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
	public function register(RegisterRequest $request)
	{
		$user = User::create([
			"first_name" => $request->first_name,
			"last_name" => $request->last_name,
			"email" => $request->email,
			"password" => Hash::make($request->password),
		]);

		$url = $this->createVerificationUrl($user);

		$user->notify((new VerifyEmailAddressNotification($url))->locale(GetUserLocaleService::getLocale($user)));

		// Send mail to marketing
		$user->notify((new UserRegisteredNotification($user))->locale(GetUserLocaleService::getLocale($user)));

		return new UserResource($user);
	}

	/**
	 * @OA\Post(
	 *	path="/auth/login",
	 *	tags={"Auth"},
	 *	summary="Log in.",
	 *	operationId="authLogin",
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="email",
	 *                  type="string",
	 *                  default="john@mail.de"
	 *              ),
	 *  			@OA\Property(
	 *                  property="password",
	 *                  type="string",
	 *                  default="password1"
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
	public function login(LoginRequest $request)
	{
		$user = User::where("email", $request->email)->first();

		if (!$user || !Hash::check($request->password, $user->password)) {
			return response()->json(["message" => __('auth.failed')], 401);
		} else if (!$user->hasVerifiedEmail()) {
			return response()->json(["message" => __('auth.email-not-verified')], 401);
		}

		Auth::attempt(['email' => $request->email, 'password' => $request->password]);

		$clientId = $request->header('clientId');
		$userClient = $user->clients()->where('client_id', $clientId);

		// ? Set the token name to either device name or device type in the future
		$token = $user->createToken("mytoken");

		// Check if the intermediate entry already exists and create/update it
		if ($userClient->exists()) {
			$user->clients()->updateExistingPivot($clientId, [
				'last_active_at' => date('Y-m-d H:i:s'),
				'login_counter' => $userClient->first()->pivot->login_counter + 1
			]);

			// If the user has no settings yet, set them (This also means that he has not logged in for the first time yet)
			if ($user->settings->isEmpty()) {
				$this->addDefaultSettings($user);
			}

			$new_user = false;
		} else {
			$user->clients()->attach($clientId, [
				'last_active_at' => date('Y-m-d H:i:s'),
				'login_counter' => 1
			]);

			// Create default set of settings for the user when first logged in
			$this->addDefaultSettings($user);

			$new_user = true;
		}

		return response()->json([
			"data" => [
				"user" => new UserResource($user),
				"settings" => SettingUserValueResource::collection(
					SettingUserValue::where("user_id", $user->id)
						->with('user')
						->with('setting')
						->with('value')
						->get()
				),
				"token" => $token->plainTextToken,
				"new_user" => $new_user
			]
		], 200);
	}

	/**
	 * @OA\Post(
	 *	path="/auth/logout",
	 *	tags={"Auth"},
	 *	summary="Log out.",
	 *	operationId="authLogout",
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
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
	 * @OA\Get(
	 *	path="/auth/user",
	 *	tags={"Auth"},
	 *	summary="Show current user.",
	 *	operationId="authUser",
	 *	security={ {"sanctum": {} }},
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
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

	/**
	 * @OA\Post(
	 *	path="/auth/forgot-password",
	 *	tags={"Auth"},
	 *	summary="Handle the forgot password functionality.",
	 *	operationId="forgotPassword",
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="email",
	 *                  type="string",
	 *              ),
	 *              required={"email"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=250,
	 *		description="Requested mail action okay, completed"
	 *	),
	 *	@OA\Response(
	 *		response=451,
	 *		description="Requested action aborted: local error in processing"
	 *	),
	 *)
	 *
	 **/
	public function forgotPassword(ForgotPasswordRequest $request)
	{
		$status = PasswordFacade::sendResetLink(
			$request->only('email')
		);

		if ($status === PasswordFacade::RESET_LINK_SENT) {
			return response(__($status), 250);
		} else {
			return response()->json([
				"errors" => [
					"status" => 451,
					"detail" => __('passwords.send_reset_link_error')
				]
			], 451);
		}
	}

	public function createVerificationUrl(User $user)
	{
		$url = URL::temporarySignedRoute(
			'verification.verify',
			Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
			[
				'id' => $user->getKey(),
				'hash' => sha1($user->getEmailForVerification()),
			]
		);

		return $url;
	}

	/**
	 * @OA\Post(
	 *	path="/auth/reset-password",
	 *	tags={"Auth"},
	 *	summary="Handle the reset password functionality.",
	 *	operationId="resetPassword",
	 * 	@OA\Parameter(
	 *		name="clientId",
	 *		required=true,
	 *		in="header",
	 * 		example="1"
	 *	),
	 * 	@OA\Parameter(
	 *		name="version",
	 *		required=true,
	 *		in="header",
	 * 		example="1.0.0"
	 *	),
	 * 	@OA\Parameter(
	 *		name="locale",
	 *		required=false,
	 *		in="header"
	 *	),
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="email",
	 *                  type="string",
	 *              ),
	 * 				@OA\Property(
	 *                  property="password",
	 *                  type="string",
	 *              ),
	 * 	 			@OA\Property(
	 *                  property="password_confirmation",
	 *                  type="string",
	 *              ),
	 * 	 			@OA\Property(
	 *                  property="token",
	 *                  type="string",
	 *              ),
	 *              required={"email"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success"
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
	public function resetPassword(ResetPasswordRequest $request)
	{
		$status = PasswordFacade::reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),
			function ($user, $password) {
				$user->forceFill([
					'password' => Hash::make($password)
				])->setRememberToken(Str::random(60));

				$user->save();

				event(new PasswordReset($user));
			}
		);

		$user = User::where('email', $request->email)->first();

		if ($status === PasswordFacade::PASSWORD_RESET) {
			// Send password reset success mail
			$user->notify((new PasswordResetSuccessfulNotification())->locale(GetUserLocaleService::getLocale($user)));

			return response(__($status), 200);
		} else {
			return response()->json([
				"errors" => [
					"status" => 400,
					"detail" => __('passwords.password_reset_error')
				]
			], 400);
		}
	}

	/**
	 * @OA\Get(
	 *	path="/auth/email/verify/{id}/{hash}",
	 *	tags={"Auth"},
	 *	summary="Handle the process of verifying a users email. (Doesn't seem to be working on swagger)",
	 *	operationId="verifyEmail",
	 *	@OA\Parameter(
	 *		name="id",
	 *		required=true,
	 *		in="path",
	 *	),
	 *	@OA\Parameter(
	 *		name="hash",
	 *		required=true,
	 *		in="path",
	 *	),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success"
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
	public function verifyEmail(CustomEmailVerificationRequest $request, $id, SendinblueService $sendinblueService)
	{
		$request->fulfill();
		$user = User::find($id);

		if(config("app.sendinblue_active")) {
			// Create the corresponding contact in sendinblue
			$response = $sendinblueService->createContact(
				$user,
				array(
					'VORNAME' => $user->first_name,
					'NACHNAME' => $user->last_name
				),
				false,
				false,
				array(
					4,
					5
				),
				true,
				array()
			);

			// Trigger the corresponding sendinblue event if the contact creation was successful
			if ($response->successful()) {
				$response = $sendinblueService->triggerEvent(
					'registered_for_betatest',
					$user,
					array(
						'firstname' => $user->first_name,
						'lastname' => $user->last_name
					)
				);
			}
		}

		$user = User::find($id);
		$user->notify((new VerificationSuccessfulNotification())->locale(GetUserLocaleService::getLocale($user)));

		return response()->json(__('auth.email-verified-successfully'), 204);
	}

	/**
	 * @OA\Post(
	 *	path="/auth/email/verification-notification",
	 *	tags={"Auth"},
	 *	summary="Resends the verification mail to the user",
	 *	operationId="resendVerificationMail",
	 *  @OA\RequestBody(
	 *      required=true,
	 *      @OA\MediaType(
	 *          mediaType="application/json",
	 *          @OA\Schema(
	 *  			@OA\Property(
	 *                  property="user_email",
	 * 					type="string",
	 *              ),
	 *              required={"user_email"}
	 *          )
	 *      )
	 *  ),
	 *
	 *	@OA\Response(
	 *		response=200,
	 *		description="Success"
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
	public function resendVerificationMail(Request $request)
	{
		$user = User::where('email', $request->user_email)->first();
		$url = $this->createVerificationUrl($user);

		$user->notify((new VerifyEmailAddressNotification($url))->locale(GetUserLocaleService::getLocale($user)));

		return response(__('auth.verification-link-sent'), 200);
	}

	public function addDefaultSettings($user)
	{

		foreach(Setting::all() as $setting) {
			$defaultValue = Value::where("designation", $setting->default_value)->first();
			$defaultValueId = $defaultValue ? $defaultValue->id : NULL;

			$user->settings()->attach([
				$setting->id => ['value_id' => $defaultValueId]
			]);
		}
	}

	public function startTrial(Request $request)
	{
		$request->user()->startTrial();
	}
}
