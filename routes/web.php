<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
// TODO
Route::get('/mailable/invitation', function () {
    $invitation = App\Models\Invitation::first();
    $notifiable = App\Models\User::first();
    $message = __('email.invited_to_bug', ['bug' => __('data.bug'), 'bugDesignation' => "testresource"]);

    return new App\Mail\InvitationReceived($notifiable, $invitation, $message);
});
// TODO
// Route::get('/mailable/unregister-invitation', function () {
//     $invitation = App\Models\Invitation::first();
//     $notifiable = App\Models\User::first();
//     $message = __('email.invited_to_bug', ['bug' => __('data.bug'), 'bugDesignation' => "testresource"]);

//     return new App\Mail\InvitationReceived($notifiable, $invitation, $message);
// });
