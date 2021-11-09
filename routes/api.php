<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;
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
	Route::post('register', [AuthController::class, "register"])->name("register");
	Route::post('login', [AuthController::class, "login"])->name("login");
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

	// Route for the chrome extention to check if the visited website has a respective project
	Route::post('/check-project', [UserController::class, "checkProject"])->name("check-project");

	// Company routes
	Route::apiResource('/companies', ProjectController::class);

	// Company prefixed routes
	Route::prefix('company/{company}')->group(function () {
		Route::apiResource('/projects', ProjectController::class);
		Route::post("/invite", [CompanyController::class, "invite"])->name("company.invite");
	});

	// Project prefixed routes
	Route::prefix('project/{project}')->group(function () {
		Route::apiResource('/statuses', StatusController::class);
		Route::post("/invite", [ProjectController::class, "invite"])->name("project.invite");
	});

	// Status prefixed routes
	Route::prefix('status/{status}')->group(function () {
		Route::apiResource('/bugs', BugController::class);
	});

	// Bug prefixed routes
	Route::prefix('bug/{bug}')->group(function () {
		Route::apiResource('/comments', CommentController::class);
		Route::apiResource('/screenshots', ScreenshotController::class);
		Route::apiResource('/attachments', AttachmentController::class);
	});

	// Download routes
	Route::get('/screenshot/{screenshot}/download', [ScreenshotController::class, "download"])->name("screenshot.download");
	Route::get('/attachment/{attachment}/download', [AttachmentController::class, "download"])->name("attachment.download");
	Route::get('/image/{image}/download', [ImageController::class, "download"])->name("image.download");

	// Invitation routes
	Route::prefix('invitation')->group(function () {
		Route::get("/status", [InvitationController::class, "statusIndex"])->name("invitation.statusIndex");
		Route::get("/status/{status}", [InvitationController::class, "statusShow"])->name("invitation.statusShow");
		Route::get("/{invitation}", [InvitationController::class, "show"])->name("invitation.show");
		Route::delete("/{invitation}", [InvitationController::class, "destroy"])->name("invitation.destroy");
		Route::post("/{invitation}/accept", [InvitationController::class, "accept"])->name("invitation.accept");
		Route::post("/{invitation}/decline", [InvitationController::class, "decline"])->name("invitation.decline");
	});

	// Miscellaneous resource routes
	Route::apiResources(
		[
			'image' => ImageController::class,
			'role' => RoleController::class,
			'priority' => PriorityController::class,
		]
	);
});
