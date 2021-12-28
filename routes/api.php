<?php

// Miscellaneous, Helpers, ...
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BugController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\ScreenshotController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserController;

// Resources
use App\Http\Resources\UserResource;

// Models
use App\Models\Company;

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
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
	// Register Routes
	Route::post('/register', [AuthController::class, "register"])->name("register");
	Route::get('/email/verify/{id}/{hash}', [AuthController::class, "verifyEmail"])->middleware('signed')->name('verification.verify');
	Route::post('/email/verification-notification', [AuthController::class, "resendVerificationMail"])->middleware('throttle:6,1')->name('verification.send');

	// Login Routes
	Route::post('/login', [AuthController::class, "login"])->name("login");

	// Password Reset Routes
	Route::post('/forgot-password', [AuthController::class, "forgotPassword"])->middleware('guest')->name('password.email');
	Route::post('/reset-password', [AuthController::class, "resetPassword"])->middleware('guest')->name('password.update');
});


/*
|--------------------------------------------------------------------------
| Private API Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(
	function () {
		Route::prefix("auth")->group(function () {
			Route::post('/logout', [AuthController::class, "logout"])->name("logout");
			Route::post('/user', [AuthController::class, "user"])->name("user");
		});
	}
);

Route::middleware(['auth:sanctum'])->group(function () {

	// Company routes
	Route::apiResource('/companies', CompanyController::class);

	// Company prefixed routes
	Route::prefix('companies/{company}')->group(function () {
		Route::apiResource('/projects', ProjectController::class);
		Route::get("/image", [CompanyController::class, "image"])->name("company.image");
		Route::get("/invitations", [CompanyController::class, "invitations"])->name("company.invitations");
		Route::post("/invite", [CompanyController::class, "invite"])->name("company.invite");
		Route::get("/users", [CompanyController::class, "users"])->name("company.users");
	});

	// Project prefixed routes
	Route::prefix('projects/{project}')->group(function () {
		Route::apiResource('/statuses', StatusController::class);
		Route::get('/image', [ProjectController::class, "image"])->name("project.image");
		Route::get('/bugs', [ProjectController::class, "bugs"])->name("project.bugs");
		Route::get("/invitations", [ProjectController::class, "invitations"])->name("project.invitations");
		Route::post('/invite', [ProjectController::class, "invite"])->name("project.invite");
		Route::get("/users", [ProjectController::class, "users"])->name("project.users");
	});

	// Status prefixed routes
	Route::prefix('statuses/{status}')->group(function () {
		Route::apiResource('/bugs', BugController::class);
	});

	// Bug prefixed routes
	Route::prefix('bugs/{bug}')->group(function () {
		Route::apiResource('/comments', CommentController::class)->except([
			'destroy'
		]);;
		Route::apiResource('/screenshots', ScreenshotController::class)->except([
			'destroy'
		]);;
		Route::apiResource('/attachments', AttachmentController::class)->except([
			'destroy'
		]);;
		Route::post('/assign-user', [BugController::class, "assignUser"])->name("bug.assign-user");
	});

	// Delete routes for screenshots, comments and attachments
	Route::delete('/screenshots/{screenshot}', [ScreenshotController::class, "destroy"])->name("screenshot.destroy");
	Route::delete('/comments/{comment}', [CommentController::class, "destroy"])->name("comment.destroy");
	Route::delete('/attachments/{attachment}', [AttachmentController::class, "destroy"])->name("attachment.destroy");

	// Download routes
	Route::get('/screenshots/{screenshot}/download', [ScreenshotController::class, "download"])->name("screenshot.download");
	Route::get('/attachments/{attachment}/download', [AttachmentController::class, "download"])->name("attachment.download");

	Route::prefix('/user')->group(function () {
		// Route for the chrome extention to check if the visited website has a respective project
		Route::post('/check-project', [UserController::class, "checkProject"])->name("user.check-project");

		// Invitation prefixed routes
		Route::prefix('invitations')->group(function () {
			Route::get("/", [InvitationController::class, "index"])->name("user.invitation.index");
			Route::get("/{invitation}", [InvitationController::class, "show"])->name("user.invitation.show");
			Route::delete("/{invitation}", [InvitationController::class, "destroy"])->name("user.invitation.destroy");
			Route::get("/{invitation}/accept", [InvitationController::class, "accept"])->name("user.invitation.accept");
			Route::get("/{invitation}/decline", [InvitationController::class, "decline"])->name("user.invitation.decline");
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
