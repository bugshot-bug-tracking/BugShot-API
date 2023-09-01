<?php

namespace App\Traits;

use App\Models\Setting;
use App\Models\SettingUserValue;
use App\Models\Value;

trait HasSettings {

    /**
     * @param $settingName
     * @return string $value
     */
    public function getSettingValueByName($settingName) {

		$setting = Setting::where('designation', $settingName)->first();

		if($setting != NULL) {
			$response = $this->getSettingValueById($setting);

			return $response;
		}

		return false;
    }


	/**
     * @param Setting $setting
     * @return string $value
     */
    public function getSettingValueById(Setting $setting) {

		if($setting != NULL) {
			$settingUserValue = SettingUserValue::where('user_id', $this->id)->where('setting_id', $setting->id)->first();

			if($settingUserValue != NULL) {
				$value = Value::find($settingUserValue->value_id);
				return $value->designation;
			} else {
				$this->settings()->attach([$setting->id => ['value_id' => NULL]]);
			}

			return NULL;
		}

		return false;
    }
}
