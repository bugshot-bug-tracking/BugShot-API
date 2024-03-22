<?php

return [

	/*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. Typically, these should include your local
    | and production domains which access your API via a frontend SPA.
    |
    */

	'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
		'%s%s',
		'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
		env('APP_URL') ? ',' . parse_url(env('APP_URL'), PHP_URL_HOST) : ''
	))),

	/*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will be
    | considered expired. If this value is null, personal access tokens do
    | not expire. This won't tweak the lifetime of first-party sessions.
    | considered expired. This will override any values set in the token's
    | "expires_at" attribute, but first-party sessions are not affected.
    |
    */

	'expiration' => 60 * 24 * 5, // 60 min * 24 hours * 5 = 5 day token duration

	/*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Sanctum can prefix new tokens in order to take advantage of numerous
    | security scanning initiatives maintained by open source platforms
    | that notify developers if they commit tokens into repositories.
    |
    | See: https://docs.github.com/en/code-security/secret-scanning/about-secret-scanning
    |
    */

	'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

	/*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Sanctum you may need to
    | customize some of the middleware Sanctum uses while processing the
    | request. You may change the middleware listed below as required.
    |
    */

	'middleware' => [
		'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
		// 'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
		'validate_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
		'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
	],

];
