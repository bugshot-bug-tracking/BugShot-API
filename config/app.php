<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [

	/*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

	'name' => env('APP_NAME', 'Laravel'),

	/*
    |--------------------------------------------------------------------------
    | Project Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your project.
    |
    */

	'projectname' => env('PROJECT_NAME', 'Laravel'),

	/*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

	'env' => env('APP_ENV', 'production'),

	/*
    | Variable to determine the max stack size of the jobs table.
	| If the stack size is reached, the admin will be notified.
    */

	'max_job_stack_size' => env('MAX_JOB_STACK_SIZE', 10),

	/*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

	'debug' => (bool) env('APP_DEBUG', false),

	/*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

	'url' => env('APP_URL', 'http://localhost'),

	'asset_url' => env('ASSET_URL', null),

	/*
    |--------------------------------------------------------------------------
    | Webpanel URL
    |--------------------------------------------------------------------------
    |
    */

	'webpanel_url' => env('APP_WEBPANEL_URL', 'https://dev.bugshot.de'),

	/*
    |--------------------------------------------------------------------------
    | Bugherd URL
    |--------------------------------------------------------------------------
    |
    */

	'bugherd_api_url' => env('BUGHERD_API_URL', 'https://www.bugherd.com/api_v2'),

	/*
    |--------------------------------------------------------------------------
    | Payment URL
    |--------------------------------------------------------------------------
    |
    */

	'payment_url' => env('APP_PAYMENT_URL', 'https://dev-payment.bugshot.de'),

	/*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    |
    */

	'stripe_api_url' => env('STRIPE_API_URL', 'https://api.stripe.com/v1'),
	'stripe_api_key' => env('STRIPE_KEY', NULL),
	'stripe_api_secret' => env('STRIPE_SECRET', NULL),
	'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET', NULL),
	'cashier_currency' => env('CASHIER_CURRENCY', 'eur'),
	'cashier_logger' => env('CASHIER_LOGGER', 'stack'),
	'cashier_currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'de_DE'),

	/*
    |--------------------------------------------------------------------------
    | Proposal URL
    |--------------------------------------------------------------------------
    |
    */

	'proposal_url' => env('APP_PROPOSAL_URL', 'https://www.bugshot.de'),

	/*
    |--------------------------------------------------------------------------
    | Sendinblue
    |--------------------------------------------------------------------------
    |
    */

	'sendinblue_active' => env('SENDINBLUE_ACTIVE', false),
	'sendinblue_ma_key' => env('SENDINBLUE_MA_KEY', null),
	'sendinblue_v2_api_url' => env('SENDINBLUE_V2_API_URL', null),
	'sendinblue_v3_api_key' => env('SENDINBLUE_V3_API_KEY', null),
	'sendinblue_v3_api_url' => env('SENDINBLUE_V3_API_URL', null),

	/*
    |--------------------------------------------------------------------------
    | Scripts
    |--------------------------------------------------------------------------
    |
    */

	'script_active' => env('SCRIPTS_ACTIVE', false),

	/*
    |--------------------------------------------------------------------------
    | TinyPNG
    |--------------------------------------------------------------------------
    |
    */

	'tinypng_active' => env('TINYPNG_ACTIVE', false),
	'tinypng_api_key' => env('TINYPNG_API_KEY', null),

	/*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

	'timezone' => env('APP_TIMEZONE', 'UTC'),

	/*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

	'locale' => 'en',

	/*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

	'fallback_locale' => 'en',

	/*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

	'faker_locale' => 'en_US',

	/*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

	'cipher' => 'AES-256-CBC',

	'key' => env('APP_KEY'),

	'previous_keys' => [
		...array_filter(
			explode(',', env('APP_PREVIOUS_KEYS', ''))
		),
	],

	/*
    |--------------------------------------------------------------------------
    | Register Mailer
    |--------------------------------------------------------------------------
    |
    | This is the recipient of the registered notification
    |
    */

	'register_mailer' => env('REGISTER_MAILER'),

	/*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

	'providers' => ServiceProvider::defaultProviders()->merge(
		[
			/*
			* Package Service Providers...
			*/

			/*
			* Application Service Providers...
			*/
			App\Providers\AppServiceProvider::class,
			App\Providers\AuthServiceProvider::class,
			App\Providers\BroadcastServiceProvider::class,
			App\Providers\EventServiceProvider::class,
			App\Providers\RouteServiceProvider::class,

		]
	)->toArray(),

	/*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

	'aliases' => Facade::defaultAliases()->merge(
		[
			// 'Example' => App\Facades\Example::class,
		]
	)->toArray(),

	/*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

	'maintenance' => [
		'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
		'store' => env('APP_MAINTENANCE_STORE', 'database'),
	],

];
