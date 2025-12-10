<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Invitation;
use App\Notifications\InvitationReceivedNotification;
use App\Services\GetUserLocaleService;
use Illuminate\Support\Facades\DB;

class ResendAllPendingInvitationNotifications extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'resenduserinvitenotifications';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Resend all the pending invitations notifications';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		DB::transaction(function () {
			$invitations = Invitation::all()->whereNull('deleted_at')->where('status_id', '=', 1);

			foreach ($invitations as $invitation) {
				$sender = User::where('id',  $invitation->sender_id)->first();
				$user = User::where('email',  $invitation->target_email)->first();

				if ($user != null) {
					$user->notify((new InvitationReceivedNotification($invitation, $user))->locale(GetUserLocaleService::getLocale($user) ?? GetUserLocaleService::getLocale($sender)));
				}
			}
		});
	}
}
