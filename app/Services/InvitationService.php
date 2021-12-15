<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\InvitationNotification;

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

        $user = User::first('email', $request->target_email);
        $user->notify(new InvitationNotification($user, $invitation));

        return $invitation;
    }
}