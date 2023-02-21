<?php

namespace App\Services;

// Miscellaneous, Helpers, ...
use Illuminate\Support\Facades\Http;

// Models
use App\Models\User;

class SendinblueService
{
    private $sendiblueV2ApiUrl;
    private $sendiblueV3ApiUrl;
    private $sendinblueMaKey;
    private $sendinblueApiKey;

    /**
     * Create a new Sendinblue instance.
     */
    public function __construct()
    {   
        $this->sendiblueV2ApiUrl = config('app.sendinblue_v2_api_url');
        $this->sendiblueV3ApiUrl = config('app.sendinblue_v3_api_url');
        $this->sendinblueMaKey = config('app.sendinblue_ma_key');
        $this->sendinblueApiKey = config('app.sendinblue_v3_api_key');
    }

    // Create a contact via the sendinblue API
    public function createContact(User $user, $attributes, $emailBlacklisted, $smsBlacklisted, $listIds, $updateEnabled, $smtpBlacklistSender) 
    {
        $header = $this->buildHeader([
            'api-key' => $this->sendinblueApiKey
        ]);

        $response = Http::withHeaders($header)->post($this->sendiblueV3ApiUrl . '/contacts', [
            'email' => $user->email,
			'attributes' => $attributes,
            'emailBlacklisted' => $emailBlacklisted,
            'smsBlacklisted' => $smsBlacklisted,
            'listIds' => $listIds,
			'updateEnabled' => $updateEnabled,
			'smtpBlacklistSender' => $smtpBlacklistSender
		]);
        
        return $response;
    }

    // Update a contact via the sendinblue API
    public function updateContact(User $user, $attributes, $emailBlacklisted, $smsBlacklisted, $listIds, $unlinkListIds, $smtpBlacklistSender) 
    {
        $header = $this->buildHeader([
            'api-key' => $this->sendinblueApiKey
        ]);
    
        $response = Http::withHeaders($header)->put($this->sendiblueV3ApiUrl . '/contacts/' . $user->email, [
            'attributes' => $attributes,
            'emailBlacklisted' => $emailBlacklisted,
            'smsBlacklisted' => $smsBlacklisted,
            'listIds' => $listIds,
            'unlinkListIds' => $unlinkListIds,
            'smtpBlacklistSender' => $smtpBlacklistSender
        ]);

        return $response;
    }
    

    // Trigger the given event on the sendinblue API
    public function triggerEvent($event, User $user, $properties) 
    {
        $header = $this->buildHeader([
            'ma-key' => $this->sendinblueMaKey
        ]);

		$response = Http::withHeaders($header)->post($this->sendiblueV2ApiUrl . '/trackEvent', [
			'properties' => $properties,
			'email' => $user->email,
			'event' => $event
		]);

        return $response;
    }

    // Build the header for a request
    private function buildHeader($attributes) {
        $header = array(
            'Accept' => 'application/json',
			'Content-Type' => 'application/json'
        );

        foreach($attributes as $key => $attribute) {
            $header[$key] = $attribute;
        }
        
        return $header;
    }
}