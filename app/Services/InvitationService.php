<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\InvitationReceivedNotification;
use App\Notifications\InvitationReceivedUnregisteredUserNotification;
use Illuminate\Support\Facades\Notification;

class InvitationService
{
    // Send the invitation to the for the respective model
    public function send($request, $model, $id, $recipient_mail, $sentbyInterface = null)
    {
        if (Auth::id() == null) {
            // Get user information if a api key was used
            $tempProject = $request->get('project');
            $sender_id = $tempProject->user_id;
        } else {
            $sender_id = Auth::id();
        }

        $invitation = $model->invitations()->create([
            "id" => $id,
            "target_email" => $recipient_mail,
            "role_id" => $request->role_id,
            "sender_id" => $sender_id,
            "status_id" => 1, // Pending
        ]);

        // Check if the recipient is a registered user or not
        $user = User::where('email', $recipient_mail)->first();
        if ($user != null) {
            $user->notify((new InvitationReceivedNotification($invitation, $user))->locale(GetUserLocaleService::getLocale($user)));
        } else {
            Notification::route('email', $recipient_mail)
                ->notify((new InvitationReceivedUnregisteredUserNotification($invitation))->locale(GetUserLocaleService::getLocale(Auth::user()))); // Using the sender (Auth::user()) to get the locale because there is not locale setting for an unregistered user. The invitee is most likely to have the same language as the sender
        }

        return $invitation;
    }
}
