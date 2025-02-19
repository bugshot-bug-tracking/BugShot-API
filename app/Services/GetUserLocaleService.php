<?php

namespace App\Services;

use App\Models\User;
use App\Models\SettingUserValue;
use Illuminate\Support\Facades\App;

class GetUserLocaleService
{
    // Retrieve the locale setting of a user, if it exists
    public static function getLocale(User $user)
    {
		$setting = SettingUserValue::where('user_id', $user->id)->whereHas('setting', function ($query) {
			return $query->where('designation', '=', 'user_settings_interface_language');
		})->first();

        return $setting ? $setting->value->designation : App::currentLocale();
    }
}
