<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocalization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('locale');

        // Sets the localization for this request if a locale is supplied
        if (in_array($locale, ['en', 'de'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
