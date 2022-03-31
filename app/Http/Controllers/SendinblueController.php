<?php

namespace App\Http\Controllers;

// Miscellaneous, Helpers, ...
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

// Models
use App\Models\User;

class SendinblueController extends Controller
{
    /**
	 * Get the number of bugs the given user has created and
	 *
	 * @return Response
	 */
	public function getNumberOfBugs(Request $request)
	{
        $user = User::where('email', $request->email)->first();
        $numberOfBugs = $user->bugs()->count();

		// Update the corresponding sendiblue contact
		$response = Http::withHeaders([
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
			'api-key' => config('app.sendinblue_v3_api_key')
		])->put(config('app.sendinblue_v3_api_url') . '/contacts/' . $request->email, [
			'attributes' => [
				'BUGS' => $numberOfBugs
			]
		]);

		// Trigger the corresponding sendinblue event and send the number of bugs
        if($response->successful()) {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'ma-key' => config('app.sendinblue_ma_key')
            ])->post(config('app.sendinblue_v2_api_url') . '/trackEvent', [

                'event' => 'transmited_bugs_figure'
            ]);
        }

		return $response;
	}
}
