<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('setting_user_value_sub_values', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('setting_user_value_id');
			$table->unsignedBigInteger('sub_value_id');

			$table->foreign('setting_user_value_id')->references('id')->on('setting_user_values')->onDelete('cascade');
			$table->foreign('sub_value_id')->references('id')->on('sub_values')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('setting_user_value_sub_values');
	}
};
