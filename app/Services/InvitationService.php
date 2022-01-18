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
    public function send($request, $model, $id) 
    {
        $invitation = $model->invitations()->create([
			"id" => $id,
            "target_email" => $request->target_email,
            "role_id" => $request->role_id,
			"sender_id" => Auth::id(),
			"status_id" => 1, // Pending
        ]);
        
        $user = User::where('email', $request->target_email)->first();
        
        if($user != null) {
            $user->notify(new InvitationReceivedNotification($invitation));
        } else {
            Notification::route('email', $request->target_email)
                ->notify(new InvitationReceivedUnregisteredUserNotification($invitation));
        }
        
        return $invitation;
    }
}

