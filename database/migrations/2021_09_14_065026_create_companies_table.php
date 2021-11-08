<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('companies', function (Blueprint $table) {
			$table->uuid('id')->primary();
			$table->string('designation');

			$table->string('color_hex')->nullable();
			$table->string('image_path')->nullable();
			// $table->string('image_id')->nullable();
			// $table->foreign('image_id')->references('id')->on('images')->onDelete('set null');

			$table->timestamps();
			$table->timestamp('deleted_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('companies');
	}
}
