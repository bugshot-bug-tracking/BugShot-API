<?php

namespace App\Traits;

trait HasCustomEvents
{
    /**
     * @param string $eventName
     * @return string $value
     */
    public function fireCustomEvent($event)
	{
		if($event) {
			return $this->fireModelEvent($event, false);
		}

		return false;
    }
}
