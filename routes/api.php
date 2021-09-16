<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BugController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\ScreenshotController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\StatusController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
	return $request->user();
});


Route::prefix('company/{company}')->group(function () {
	Route::get("/users", [CompanyController::class, "users"]);
	Route::get("/projects", [CompanyController::class, "projects"]);
});

Route::prefix('project/{project}')->group(function () {
	Route::get("/statuses", [ProjectController::class, "statuses"]);
	Route::get("/bugs", [ProjectController::class, "bugs"]);
	Route::get("/users", [ProjectController::class, "users"]);
});

Route::prefix('status/{status}')->group(function () {
	Route::get("/bugs", [StatusController::class, "bugs"]);
});

Route::prefix('bug/{bug}')->group(function () {
	Route::get("/attachments", [BugController::class, "attachments"]);
	Route::get("/screenshots", [BugController::class, "screenshots"]);
	Route::get("/comments", [BugController::class, "comments"]);
});



Route::apiResources(
	[
		'company' => CompanyController::class,
		'project' => ProjectController::class,
		'status' => StatusController::class,
		'bug' => BugController::class,
		'image' => ImageController::class,
		'role' => RoleController::class,
		'priority' => PriorityController::class,
		'attachment' => AttachmentController::class,
		'screenshot' => ScreenshotController::class,
		'comment' => CommentController::class,
	],
	["missing" => (function (Request $request) {
		return response()->json([
			"message" => "Resource not found.",
			"errors" => "Resource not found."
		], 404);
	})]
);
