<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingAddressesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('billing_addresses', function (Blueprint $table) {
			$table->uuid('id')->primary();

			$table->string('billing_addressable_id')->unique();
			$table->string('billing_addressable_type');

			$table->string('stripe_id')->nullable();
			$table->string('street');
			$table->string('housenumber');
			$table->string('city');
			$table->string('state');
			$table->string('zip');
			$table->string('country');
			$table->string('tax_id');

			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('billing_addresses');
	}
}
