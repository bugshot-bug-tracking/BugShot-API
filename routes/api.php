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

	Route::post('/check-project', [UserController::class, "checkProject"]);

	Route::prefix('user')->group(function () {
		Route::get("/companies", [UserController::class, "companies"])->name("user.companies");
		Route::get("/company/{company}/projects", [UserController::class, "companyProjects"])->name("user.company.projects");
		// ->missing(function (Request $request) { //! need a better solution for the other routes
		// 	return response()->json([
		// 		"errors" => [
		// 			"status" => 404,
		// 			"source" => $request->getPathInfo(),
		// 			"detail" => "Company not found."
		// 		]
		// 	], 404);
		// });
	});

	Route::prefix('company/{company}')->group(function () {
		Route::get("/users", [CompanyController::class, "users"])->name("company.users");
		Route::get("/projects", [CompanyController::class, "projects"])->name("company.projects");
		Route::post("/invite", [CompanyController::class, "invite"])->name("company.invite");
	});

	Route::prefix('project/{project}')->group(function () {
		Route::get("/statuses", [ProjectController::class, "statuses"])->name("project.statuses");
		Route::get("/bugs", [ProjectController::class, "bugs"])->name("project.bugs");
		Route::get("/users", [ProjectController::class, "users"])->name("project.users");
		Route::post("/invite", [ProjectController::class, "invite"])->name("project.invite");
	});

	Route::prefix('status/{status}')->group(function () {
		Route::get("/bugs", [StatusController::class, "bugs"])->name("status.bugs");
	});

	Route::prefix('bug/{bug}')->group(function () {
		Route::get("/attachments", [BugController::class, "attachments"])->name("bug.attachments");
		Route::get("/screenshots", [BugController::class, "screenshots"])->name("bug.screenshots");
		Route::get("/comments", [BugController::class, "comments"])->name("bug.comments");
	});

	Route::get('/screenshot/{screenshot}/download', [ScreenshotController::class, "download"])->name("screenshot.download");
	Route::get('/attachment/{attachment}/download', [AttachmentController::class, "download"])->name("attachment.download");
	Route::get('/image/{image}/download', [ImageController::class, "download"])->name("image.download");

	Route::prefix('invitation/{invitation}')->group(function () {
		Route::post("/accept", [InvitationController::class, "accept"])->name("invitation.accept");
		Route::post("/decline", [InvitationController::class, "decline"])->name("invitation.decline");
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
			'invitation' => InvitationController::class,
		],
		// ["missing" => (function (Request $request) {
		// 	return response()->json([
		// 		"errors" => [
		// 			"status" => 404,
		// 			"source" => $request->getPathInfo(),
		// 			"detail" => "Resource with specified id not found."
		// 		]
		// 	], 404);
		// })]
	);
});
