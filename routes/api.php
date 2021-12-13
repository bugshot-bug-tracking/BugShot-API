<?php

// Miscellaneous, Helpers, ...
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ImageController;
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

// Route::prefix('auth')->middleware(['check_version'])->group(function () {
Route::prefix('auth')->group(function () {
	Route::post('register', [AuthController::class, "register"])->name("register");
	Route::post('login', [AuthController::class, "login"])->name("login");

	// Password Reset Routes
	Route::post('/forgot-password', [AuthController::class, "forgotPassword"])->middleware('guest')->name('password.email');
	Route::post('/reset-password', [AuthController::class, "resetPassword"])->middleware('guest')->name('password.update');

	// TODO
	// Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
	// 	$request->fulfill();
	
	// 	return redirect('/home');
	// })->middleware(['auth', 'signed'])->name('verification.verify');
});


/*
|--------------------------------------------------------------------------
| Private API Routes
|--------------------------------------------------------------------------
*/
// Route::middleware(['auth:sanctum', 'check_version'])->group(
Route::middleware(['auth:sanctum'])->group(
	function () {
		Route::prefix("auth")->group(function () {
			Route::post('/logout', [AuthController::class, "logout"])->name("logout");
			Route::post('/user', [AuthController::class, "user"])->name("user");
		});
	}
);

// Route::middleware(['auth:sanctum', 'check_version'])->group(function () { 
Route::middleware(['auth:sanctum'])->group(function () {

	// Route for the chrome extention to check if the visited website has a respective project
	Route::post('/check-project', [UserController::class, "checkProject"])->name("check-project");

	// Company routes
	Route::apiResource('/companies', CompanyController::class);

	// Company prefixed routes
	Route::prefix('companies/{company}')->group(function () {
		Route::apiResource('/projects', ProjectController::class);
		Route::get("/image", [CompanyController::class, "image"])->name("company.image");
		Route::post("/invite", [CompanyController::class, "invite"])->name("company.invite");
		Route::get("/users", [CompanyController::class, "users"])->name("company.users");
	});

	// Project prefixed routes
	Route::prefix('projects/{project}')->group(function () {
		Route::apiResource('/statuses', StatusController::class);
		Route::get('/image', [ProjectController::class, "image"])->name("project.image");
		Route::get('/bugs', [ProjectController::class, "bugs"])->name("project.bugs");
		Route::post('/invite', [ProjectController::class, "invite"])->name("project.invite");
		Route::get("/users", [ProjectController::class, "users"])->name("project.users");
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
	});

	// Download routes
	Route::get('/screenshots/{screenshot}/download', [ScreenshotController::class, "download"])->name("screenshot.download");
	Route::get('/attachments/{attachment}/download', [AttachmentController::class, "download"])->name("attachment.download");
	Route::get('/images/{image}/download', [ImageController::class, "download"])->name("image.download");

	// Invitation routes
	Route::prefix('invitations')->group(function () {
		Route::get("/statuses", [InvitationController::class, "statusIndex"])->name("invitation.statusIndex");
		Route::get("/statuses/{status}", [InvitationController::class, "statusShow"])->name("invitation.statusShow");
		Route::get("/{invitation}", [InvitationController::class, "show"])->name("invitation.show");
		Route::delete("/{invitation}", [InvitationController::class, "destroy"])->name("invitation.destroy");
		Route::post("/{invitation}/accept", [InvitationController::class, "accept"])->name("invitation.accept");
		Route::post("/{invitation}/decline", [InvitationController::class, "decline"])->name("invitation.decline");
	});

	// Miscellaneous resource routes
	Route::apiResources(
		[
			'images' => ImageController::class,
			'roles' => RoleController::class,
			'priorities' => PriorityController::class,
		]
	);
});
