<?php

namespace App\Observers;

// Models
use App\Models\Action;
use App\Models\Bug;

class BugObserver
{
    /**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
    public $afterCommit = true;

    /**
     * Handle the Bug "created" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function created(Bug $bug)
    {
		$action = new Action();
		$actionId = $action->getIdByName("bug_created");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => $bug->creator ? $bug->creator->id : NULL,
					"args" => json_encode([
						$bug->designation,
						$bug->creator->fullName()
						]
					)
				]
			);
		}
    }

    /**
     * Handle the Bug "updated" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function updated(Bug $bug)
    {
		$dirtyAttributeMessage = $this->buildDirtyAttributesMessage($bug);

		$action = new Action();
		$actionId = $action->getIdByName("bug_updated");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => $bug->creator ? $bug->creator->id : NULL,
					"args" => json_encode([
							$bug->designation,
							$bug->creator->fullName(),
							$dirtyAttributeMessage
						]
					)
				]
			);
		}
    }

    /**
     * Handle the Bug "deleted" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function deleted(Bug $bug)
    {
		$action = new Action();
		$actionId = $action->getIdByName("bug_deleted");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => $bug->creator ? $bug->creator->id : NULL,
					"args" => json_encode([
							$bug->designation,
							$bug->creator->fullName()
						]
					)
				]
			);
		}
    }

    /**
     * Handle the Bug "restored" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function restored(Bug $bug)
    {
        //
    }

    /**
     * Handle the Bug "forceDeleted" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function forceDeleted(Bug $bug)
    {
        //
    }

	/**
     * Handle the Bug "movedToNewGroup" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function movedToNewGroup(Bug $bug)
    {
		$dirtyAttributeMessage = $this->buildDirtyAttributesMessage($bug);

		$action = new Action();
		$actionId = $action->getIdByName("bug_moved_to_new_group");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => $bug->creator ? $bug->creator->id : NULL,
					"args" => json_encode([
							$bug->designation,
							$bug->creator->fullName(),
							$dirtyAttributeMessage
						]
					)
				]
			);
		}
    }

	/**
     * Handle the Bug "accessTokenGenerated" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function accessTokenGenerated(Bug $bug)
    {
		$action = new Action();
		$actionId = $action->getIdByName("bug_access_token_generated");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => $bug->creator ? $bug->creator->id : NULL,
					"args" => json_encode([
							$bug->designation,
							$bug->creator->fullName()
						]
					)
				]
			);
		}
    }

	/**
     * Handle the Bug "accessTokenDeleted" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function accessTokenDeleted(Bug $bug)
    {
		$action = new Action();
		$actionId = $action->getIdByName("bug_access_token_deleted");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => $bug->creator ? $bug->creator->id : NULL,
					"args" => json_encode([
							$bug->designation,
							$bug->creator->fullName()
						]
					)
				]
			);
		}
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
