<?php

namespace App\Observers;

// Misc
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\Action;
use App\Models\AccessToken;

class AccessTokenObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the AccessToken "accessTokenCreated" event.
     *
     * @param  AccessToken  $accessToken
     * @return void
     */
    public function accessTokenCreated(AccessToken $accessToken)
    {
		$action = new Action();
		$actionId = $action->getIdByName("access_token_created");
		if($actionId)
		{
			$accessToken->history()->attach(
				$actionId,
				[
					"user_id" => $accessToken->creator ? $accessToken->creator->id : NULL,
					"args" => json_encode([
							$accessToken->designation,
							$accessToken->creator->fullName()
						]
					)
				]
			);
		}
    }

    /**
     * Handle the AccessToken "accessTokenUpdated" event.
     *
     * @param  AccessToken  $accessToken
     * @return void
     */
    public function accessTokenUpdated(AccessToken $accessToken)
    {
		$dirtyAttributeMessage = $this->buildDirtyAttributesMessage($accessToken);

		$action = new Action();
		$actionId = $action->getIdByName("access_token_updated");
		if($actionId)
		{
			$accessToken->history()->attach(
				$actionId,
				[
					"user_id" => Auth::id(),
					"args" => json_encode([
							$accessToken->designation,
							Auth::user()->fullName(),
							$dirtyAttributeMessage
						]
					)
				]
			);
		}
    }

    /**
     * Handle the AccessToken "accessTokenDeleted" event.
     *
     * @param  AccessToken  $accessToken
     * @return void
     */
    public function accessTokenDeleted(AccessToken $accessToken)
    {
		$action = new Action();
		$actionId = $action->getIdByName("access_token_deleted");
		if($actionId)
		{
			$accessToken->history()->attach(
				$actionId,
				[
					"user_id" => Auth::id(),
					"args" => json_encode([
							$accessToken->designation,
							Auth::user()->fullName()
						]
					)
				]
			);
		}
    }

    /**
     * Handle the AccessToken "accessTokenRestored" event.
     *
     * @param  AccessToken  $accessToken
     * @return void
     */
    public function accessTokenRestored(AccessToken $accessToken)
    {
        //
    }

    /**
     * Handle the AccessToken "accessTokenForceDeleted" event.
     *
     * @param  AccessToken  $accessToken
     * @return void
     */
    public function accessTokenForceDeleted(AccessToken $accessToken)
    {
        //
    }

	private function buildDirtyAttributesMessage($resource)
	{
		$dirtyAttributes = $resource->getDirty();
		unset($dirtyAttributes['updated_at']);
		$dirtyAttributeMessage = "";
		foreach($dirtyAttributes as $dirtyAttribute => $dirtyValue)
		{
			$newValue = $dirtyValue;
			$oldValue = $resource->getOriginal($dirtyAttribute);
			if($dirtyAttributeMessage == "")
			{
				$dirtyAttributeMessage .= "$dirtyAttribute: '$oldValue' => '$newValue'";
			} else
			{
				$dirtyAttributeMessage .= ", $dirtyAttribute: '$oldValue' => '$newValue'";
			}
		}

		return $dirtyAttributeMessage;
	}
}
