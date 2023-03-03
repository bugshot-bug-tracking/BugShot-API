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
use App\Models\Organization;

// Requests
use App\Http\Requests\CustomEmailVerificationRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Notifications\UserRegisteredNotification;

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
				$user->settings()->attach($this->getDefaultSettings());

				// Also create the initial default organization for him
				$organization = Organization::create([
					"id" => $this->setId($request),
					"user_id" => $user->id,
					"designation" => __('data.my-organization', [], GetUserLocaleService::getLocale($user)) . " (" . $user->first_name . " " . $user->last_name . ")"
				]);

				// Also add the owner to the organization user role table in order to be able to store the subscription
				$organization->users()->attach($user->id, ['role_id' => 0]);
			}
		} else {
			$user->clients()->attach($clientId, [
				'last_active_at' => date('Y-m-d H:i:s'),
				'login_counter' => 1
			]);

			// Create default set of settings for the user when first logged in
			$user->settings()->attach($this->getDefaultSettings());

			// Also create the initial default organization for him
			Organization::create([
				"id" => $this->setId($request),
				"user_id" => $user->id,
				"designation" => __('data.my-organization', [], GetUserLocaleService::getLocale($user))
			]);
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
				"token" => $token->plainTextToken
			]
		], 200);
	}

	// Returns a set of default settings with their default values
	private function getDefaultSettings()
	{
		$defaultSettings = [
			1 => ['value_id' => 1], // company_filter_alphabetical: az
			2 => ['value_id' => 3], // company_filter_creation: newest_first
			3 => ['value_id' => 6], // company_filter_last_updated: ascending
			4 => ['value_id' => 1], // project_filter_alphabetical:az
			5 => ['value_id' => 3], // project_filter_creation: newest_first
			6 => ['value_id' => 6], // project_filter_last_updated: ascending
			7 => ['value_id' => 1], // bug_filter_alphabetical: az
			8 => ['value_id' => 3], // bug_filter_creation: newest_first
			9 => ['value_id' => 7], // bug_filter_priority: critical_first
			10 => ['value_id' => 9], // bug_filter_deadline: ending_first
			11 => ['value_id' => NULL], // bug_filter_assigned_to: NULL (Filter not implemeneted yet)
			12 => ['value_id' => 13], // user_settings_interface_language: en
			13 => ['value_id' => 18], // user_settings_show_ui_elements: show_all
			14 => ['value_id' => 25], // user_settings_receive_mail_notifications: receive_notifications_via_app
			14 => ['value_id' => 26], // user_settings_receive_mail_notifications: receive_notifications_via_mail
			15 => ['value_id' => 27], // user_settings_select_notifications: every_notification
			16 => ['value_id' => 36] // user_settings_darkmode: light_mode
		];

		return $defaultSettings;
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
	 *                  property="user_id",
	 * 					type="integer",
	 *  				format="int64",
	 *              ),
	 *              required={"user_id"}
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
		$user = User::find($request->user_id);
		$url = $this->createVerificationUrl($user);

		$user->notify((new VerifyEmailAddressNotification($url))->locale(GetUserLocaleService::getLocale($user)));

		return response(__('auth.verification-link-sent'), 200);
	}

	public function addDefaultSettings($user)
	{
		$user->settings()->attach([
			1 => ['value_id' => 1], // company_filter_alphabetical: az
			2 => ['value_id' => 3], // company_filter_creation: newest_first
			3 => ['value_id' => 6], // company_filter_last_updated: ascending
			4 => ['value_id' => 1], // project_filter_alphabetical:az
			5 => ['value_id' => 3], // project_filter_creation: newest_first
			6 => ['value_id' => 6], // project_filter_last_updated: ascending
			7 => ['value_id' => 1], // bug_filter_alphabetical: az
			8 => ['value_id' => 3], // bug_filter_creation: newest_first
			9 => ['value_id' => 7], // bug_filter_priority: critical_first
			10 => ['value_id' => 9], // bug_filter_deadline: ending_first
			11 => ['value_id' => NULL], // bug_filter_assigned_to: NULL (Filter not implemeneted yet)
			12 => ['value_id' => 13], // user_settings_interface_language: en
			13 => ['value_id' => 18], // user_settings_show_ui_elements: show_all
			14 => ['value_id' => 21], // user_settings_receive_mail_notifications: receive_notifications_everywhere
			15 => ['value_id' => 23], // user_settings_select_notifications: every_notification
			16 => ['value_id' => 25] // user_settings_darkmode: light_mode
		]);
	}

	// public function addSubValues($userId, $settingId, $subValueArray)
	// {
	// 	$settingUserValue = SettingUserValue::where('user_id', $userId)->where('setting_id', $settingId)->first();
	// 	foreach($subValueArray as $subValue) {
	// 		$settingUserValue->subValues()->attach($subValue);
	// 	}
	// }

	public function startTrial(Request $request)
	{
		$request->user()->startTrial();
	}
}
