<?php

// Miscellaneous, Helpers, ...
use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BugController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\ScreenshotController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\MarkerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\SendinblueController;
use App\Http\Controllers\BillingAddressController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\UrlController;
use Illuminate\Http\Request;
use App\Models\Url;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| Debug API Route for Sentry
|--------------------------------------------------------------------------
*/

Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::get('/mail', function () {
    $user = App\Models\User::find(1);
	$url = config('app.webpanel_url') . '/auth/verify/' . $user->id . '/token';
    return new App\Mail\VerifyEmailAddress($user, $url);
});

Route::prefix('auth')->group(function () {
	// Register Routes
	Route::post('/register', [AuthController::class, "register"])->middleware('check.version')->name("register");
	Route::get('/email/verify/{id}/{hash}', [AuthController::class, "verifyEmail"])->middleware('signed')->name('verification.verify');
	Route::post('/email/verification-notification', [AuthController::class, "resendVerificationMail"])->middleware('throttle:6,1')->name('verification.send');

	// Login Routes
	Route::post('/login', [AuthController::class, "login"])->middleware('check.version')->name("login");

	// Password Reset Routes
	Route::post('/forgot-password', [AuthController::class, "forgotPassword"])->middleware(['guest', 'check.version'])->name('password.email');
	Route::post('/reset-password', [AuthController::class, "resetPassword"])->middleware(['guest', 'check.version'])->name('password.update');
});

// Sendinblue specific routes
Route::prefix('sendinblue')->group(function () {
	Route::post("/contact/number-of-bugs", [SendinblueController::class, "getNumberOfBugs"])->name("sendinblue.number-of-bugs");
});

// Feedback Routes
Route::post('/feedbacks', [FeedbackController::class, "store"])->middleware('check.version')->name("feedback.store");

/*
|--------------------------------------------------------------------------
| Private API Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'check.version'])->group(
	function () {
		Route::prefix("auth")->group(function () {
			Route::post('/logout', [AuthController::class, "logout"])->name("logout");
			Route::get('/user', [AuthController::class, "user"])->name("user");
		});
	}
);

Route::middleware(['auth:sanctum', 'check.version'])->group(function () {

	// Organization resource routes
	Route::apiResource('/organizations', OrganizationController::class);

	// Organization prefixed routes
	Route::prefix('organizations/{organization}')->group(function () {
		Route::get("/invitations", [OrganizationController::class, "invitations"])->name("organization.invitations");
		Route::post("/invite", [OrganizationController::class, "invite"])->name("organization.invite");
		Route::get("/users", [OrganizationController::class, "users"])->name("organization.users");
		Route::delete("/users/{user}", [OrganizationController::class, "removeUser"])->name("organization.remove-user");
	});

	// Company resource routes
	Route::apiResource('/companies', CompanyController::class);

	// Company prefixed routes
	Route::prefix('companies/{company}')->group(function () {
		Route::apiResource('/projects', ProjectController::class);
		Route::get("/image", [CompanyController::class, "image"])->name("company.image");
		Route::get("/invitations", [CompanyController::class, "invitations"])->name("company.invitations");
		Route::post("/invite", [CompanyController::class, "invite"])->name("company.invite");
		Route::get("/users", [CompanyController::class, "users"])->name("company.users");
		Route::put("/users/{user}", [CompanyController::class, "updateUserRole"])->name("company.update-user-role");
		Route::delete("/users/{user}", [CompanyController::class, "removeUser"])->name("company.remove-user");
	});

	// Project prefixed routes
	Route::prefix('projects/{project}')->group(function () {
		Route::apiResource('/statuses', StatusController::class);
		Route::get('/image', [ProjectController::class, "image"])->name("project.image");
		Route::get('/bugs', [ProjectController::class, "bugs"])->name("project.bugs");
		Route::get('/markers', [ProjectController::class, "markers"])->name("project.markers");
		Route::get("/invitations", [ProjectController::class, "invitations"])->name("project.invitations");
		Route::post('/invite', [ProjectController::class, "invite"])->name("project.invite");
		Route::get("/users", [ProjectController::class, "users"])->name("project.users");
		Route::put("/users/{user}", [ProjectController::class, "updateUserRole"])->name("project.update-user-role");
		Route::delete("/users/{user}", [ProjectController::class, "removeUser"])->name("project.remove-user");
	});

	// Status prefixed routes
	Route::prefix('statuses/{status}')->group(function () {
		Route::apiResource('/bugs', BugController::class);
	});

	// Bug prefixed routes
	Route::prefix('bugs/{bug}')->group(function () {
		Route::apiResource('/comments', CommentController::class);
		Route::apiResource('/screenshots', ScreenshotController::class);
		Route::apiResource('/attachments', AttachmentController::class);
		Route::post('/assign-user', [BugController::class, "assignUser"])->name("bug.assign-user");
		Route::get("/users", [BugController::class, "users"])->name("bug.users");
		Route::delete("/users/{user}", [BugController::class, "removeUser"])->name("bug.remove-user");
	});

	// Screenshot prefixed routes
	Route::prefix('screenshots/{screenshot}')->group(function () {
		Route::apiResource('/markers', MarkerController::class);
	});

	// User resource routes
	Route::apiResource('/users', UserController::class);

	// User prefixed routes
	Route::prefix('/users/{user}')->group(function () {
		// Route for the chrome extension to check if the visited website has a respective project
		Route::post('/check-project', [UserController::class, "checkProject"])->name("user.check-project");

		// Invitation prefixed routes
		Route::prefix('invitations')->group(function () {
			Route::get("/", [InvitationController::class, "index"])->name("user.invitation.index");
			Route::get("/{invitation}", [InvitationController::class, "show"])->name("user.invitation.show");
			Route::delete("/{invitation}", [InvitationController::class, "destroy"])->name("user.invitation.destroy");
			Route::get("/{invitation}/accept", [InvitationController::class, "accept"])->name("user.invitation.accept");
			Route::get("/{invitation}/decline", [InvitationController::class, "decline"])->name("user.invitation.decline");
		});

		// Setting prefixed routes
		Route::prefix('settings')->group(function () {
			Route::get("/", [UserController::class, "settings"])->name("user.setting.index");
			Route::put("/{invitation}", [UserController::class, "updateSetting"])->name("user.setting.update");
		});
	});

	// Stripe prefixed routes
	Route::prefix('stripe')->group(function () {
		Route::get('/customer/{customer}', [StripeController::class, "getStripeCustomer"])->name("user.stripe.get-stripe-customer");
		Route::post('/customer', [StripeController::class, "createStripeCustomer"])->name("user.stripe.create-stripe-customer");
		Route::get('/balance', [StripeController::class, "showBalance"])->name("user.stripe.show-balance");
		Route::get('/setup-intent-form', [StripeController::class, "showSetupIntentForm"])->name("user.stripe.show-setup-intent-form");
		Route::post('/subscription', [StripeController::class, "createSubscription"])->name("user.stripe.create-subscription");
		Route::post('/subscription/{subscription}/change-quantity', [StripeController::class, "changeSubscriptionQuantity"])->name("user.stripe.subscription.change-quantity");
		Route::post('/payment-methods', [StripeController::class, "getPaymentMethods"])->name("user.stripe.get-payment-methods");
		// Product prefixed routes
		Route::prefix('/products')->group(function () {
			Route::get('/', [StripeController::class, "listProducts"])->name("stripe.products.list");
		});
	});

	// Polymorphic Url routes
	Route::prefix('{type}/{id}')->group(function () {
		Route::apiResource('/urls', UrlController::class);
	});

	// Billing address routes
	Route::prefix('billing-addresses')->group(function () {
		Route::post("/{type}/{id}", [BillingAddressController::class, "store"])->name("billing-address.store");
		Route::put("/{billing_address}", [BillingAddressController::class, "update"])->name("billing-address.update");

		Route::prefix('/{billing_address}/stripe')->group(function () {
			Route::get('/customer/{customer}', [StripeController::class, "getStripeCustomer"])->name("billing-address.stripe.get-stripe-customer");
			Route::post('/customer', [StripeController::class, "createStripeCustomer"])->name("billing-address.stripe.create-stripe-customer");
			Route::get('/balance', [StripeController::class, "showBalance"])->name("billing-address.stripe.show-balance");
			Route::get('/setup-intent-form', [StripeController::class, "showSetupIntentForm"])->name("billing-address.stripe.show-setup-intent-form");
			Route::post('/subscription', [StripeController::class, "createSubscription"])->name("billing-address.stripe.create-subscription");
			Route::post('/subscription/{subscription}/change-quantity', [StripeController::class, "changeSubscriptionQuantity"])->name("billing-address.stripe.subscription.change-quantity");
			Route::post('/payment-methods', [StripeController::class, "getPaymentMethods"])->name("billing-address.stripe.get-payment-methods");
			Route::get('/subscriptions', [StripeController::class, "listSubscriptions"])->name("billing-address.stripe.list-subscriptions");
		});
	});
	/*
	|--------------------------------------------------------------------------
	| Administrative API Routes
	|--------------------------------------------------------------------------
	*/
	Route::prefix('/administration')->group(function () {
		Route::apiResources(
			[
				'roles' => RoleController::class,
				'priorities' => PriorityController::class,
			]
		);
	});
});
