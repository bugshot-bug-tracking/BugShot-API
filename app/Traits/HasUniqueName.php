<?php

namespace App\Traits;

trait HasUniqueName
{
    /**
     * @param $actionName
     * @return string $value
     */
    public function getIdByName($name) {

		$model = $this::where('designation', $name)->first();

		if($model == NULL)
		{
			return false;
		}

		return $model->id;
    }
}
