<?php

namespace App\Services;

use App\Models\User;
use App\Models\SettingUserValue;
use Illuminate\Support\Facades\App;

class GetUserLocaleService
{
    // Store a newly created attachment on the server.
    public static function getLocale(User $user)
    {
		$setting = SettingUserValue::where('user_id', $user->id)->whereHas('setting', function ($query) {
			return $query->where('designation', '=', 'user_settings_interface_language');
		})->first();

        return $setting ? $setting->value->designation : App::currentLocale();
    }
}
