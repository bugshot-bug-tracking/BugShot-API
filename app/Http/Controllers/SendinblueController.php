<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Models
use App\Models\User;

// Services
use App\Services\SendinblueService;

class SendinblueController extends Controller
{
    /**
	 * Get the number of bugs the given user has created and send it to Sendinblue
	 *
	 * @return Response
	 */
	public function getNumberOfBugs(Request $request, SendinblueService $sendinblueService)
	{
		Log::info($request);
	
        $user = User::where('email', $request->email)->first();
        $numberOfBugs = $user->bugs()->count();

		// Update the corresponding contact in sendinblue
		$response = $sendinblueService->updateContact(
			$user, 
			array(
				'BUGS' => $numberOfBugs
			),
			false,
			false,
			array(),
			array(),
			array()
		);
				
		// Trigger the corresponding sendinblue event if the contact creation was successful
		if($response->successful()) {
			$response = $sendinblueService->triggerEvent(
				'transmited_bugs_figure', 
				$user, 
				array()
			);
		}

		return $response;
	}
}
