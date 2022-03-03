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
    public function send($request, $model, $id = null, $recipient_mail) 
    {
        $invitation = $model->invitations()->create([
			"id" => $id,
            "target_email" => $recipient_mail,
            "role_id" => $request->role_id,
			"sender_id" => Auth::id(),
			"status_id" => 1, // Pending
        ]);
        
        $user = User::where('email', $recipient_mail)->first();
        
        if($user != null) {
            $user->notify(new InvitationReceivedNotification($invitation));
        } else {
            Notification::route('email', $recipient_mail)
                ->notify(new InvitationReceivedUnregisteredUserNotification($invitation));
        }
        
        return $invitation;
    }
}

