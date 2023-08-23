<?php

namespace App\Services;

class NotificationService
{
    // Delete a notification
    public function delete($user, $notification)
    {
		// Delete the notification
		$val = $user->notifications()->find($notification->id)->delete();

		return $val;
    }
}
