<?php

namespace App\Observers;

// Misc
use Illuminate\Support\Facades\Auth;

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
     * Handle the Bug "bugCreated" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function bugCreated(Bug $bug)
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
     * Handle the Bug "bugUpdated" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function bugUpdated(Bug $bug)
    {
		$dirtyAttributeMessage = $this->buildDirtyAttributesMessage($bug);

		$action = new Action();
		$actionId = $action->getIdByName("bug_updated");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => Auth::id(),
					"args" => json_encode([
							$bug->designation,
							Auth::user()->fullName(),
							$dirtyAttributeMessage
						]
					)
				]
			);
		}
    }

    /**
     * Handle the Bug "bugDeleted" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function bugDeleted(Bug $bug)
    {
		$action = new Action();
		$actionId = $action->getIdByName("bug_deleted");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => Auth::id(),
					"args" => json_encode([
							$bug->designation,
							Auth::user()->fullName()
						]
					)
				]
			);
		}
    }

    /**
     * Handle the Bug "bugRestored" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function bugRestored(Bug $bug)
    {
        //
    }

    /**
     * Handle the Bug "bugForceDeleted" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function bugForceDeleted(Bug $bug)
    {
        //
    }

	/**
     * Handle the Bug "bugMovedToNewProject" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function bugMovedToNewProject(Bug $bug)
    {
		$dirtyAttributeMessage = $this->buildDirtyAttributesMessage($bug);

		$action = new Action();
		$actionId = $action->getIdByName("bug_moved_to_new_project");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => Auth::id(),
					"args" => json_encode([
							$bug->designation,
							Auth::user()->fullName(),
							$dirtyAttributeMessage
						]
					)
				]
			);
		}
    }

	/**
     * Handle the Bug "bugArchived" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function bugArchived(Bug $bug)
    {
		$dirtyAttributeMessage = $this->buildDirtyAttributesMessage($bug);

		$action = new Action();
		$actionId = $action->getIdByName("bug_archived");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => Auth::id(),
					"args" => json_encode([
							$bug->designation,
							Auth::user()->fullName(),
							$dirtyAttributeMessage
						]
					)
				]
			);
		}
    }

	/**
     * Handle the Bug "bugStatusChanged" event.
     *
     * @param  Bug  $bug
     * @return void
     */
    public function bugStatusChanged(Bug $bug)
    {
		$dirtyAttributeMessage = $this->buildDirtyAttributesMessage($bug);

		$action = new Action();
		$actionId = $action->getIdByName("bug_status_changed");
		if($actionId)
		{
			$bug->history()->attach(
				$actionId,
				[
					"user_id" => Auth::id(),
					"args" => json_encode([
							$bug->designation,
							Auth::user()->fullName(),
							$dirtyAttributeMessage
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
