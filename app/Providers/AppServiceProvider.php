<?php

namespace App\Providers;

use App\Models\BillingAddress;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Tinify\Tinify;
use Laravel\Cashier\Cashier;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Schema::defaultStringLength(255);

		Relation::enforceMorphMap([
			'organization' => 'App\Models\Organization',
			'user' => 'App\Models\User',
		]);

		Tinify::setKey(config('app.tinypng_api_key'));
		Cashier::useCustomerModel(BillingAddress::class);
		Cashier::calculateTaxes();
	}
}
