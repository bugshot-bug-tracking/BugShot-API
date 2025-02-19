<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use App\Mail\VerifyEmailAddress;

// Route::get('/mailable/invitation', function () {
//     $invitation = App\Models\Invitation::first();
//     $notifiable = App\Models\User::first();
//     $message = __('email.invited_to_bug', ['bug' => __('data.bug'), 'bugDesignation' => "testresource"]);

//     return new App\Mail\InvitationReceived($notifiable, $invitation, $message);
// });

// Route::get('/mailable/unregister-invitation', function () {
//     $invitation = App\Models\Invitation::first();
//     $message = __('email.invited_to_bug', ['bug' => __('data.bug'), 'bugDesignation' => "testresource"]);

//     $invitation = $invitation;
//     $entryMessage = $message;
//     $registerUrl = 'www.test.de';

//     return new App\Mail\InvitationReceivedUnregisteredUser($invitation, $entryMessage, $registerUrl);
// });

// Route::get('/mailable/reset-password-link', function () {
//     $notifiable = App\Models\User::first();
//     $url = config('app.webpanel_url') . '?token=1234455';

//     return new App\Mail\ResetPasswordLink($notifiable, $url);
// });

// Route::get('/mailable/verify', function () {

//     $user = App\Models\User::first();
//     $url = 'www.test.de/12345';
//     return new App\Mail\VerifyEmailAddress($user, $url);
// });

Route::get('/mailable/project-summary', function () {

	$user = App\Models\User::find(1);
	$project = App\Models\Project::find("CCCCCCCC-CCCC-CCCC-CCCC-CCCCCCCCCCCC");

	return new App\Mail\ProjectSummary($user, "de", $project);
});
