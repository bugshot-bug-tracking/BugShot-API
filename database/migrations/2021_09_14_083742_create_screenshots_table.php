<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScreenshotsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('screenshots', function (Blueprint $table) {
			$table->uuid('id')->primary();

			$table->string('bug_id');
			$table->foreign('bug_id')->references('id')->on('bugs')->onDelete('cascade');

			$table->string('designation');
			$table->text('url');

			$table->integer('position_x')->nullable();
			$table->integer('position_y')->nullable();

			$table->integer('web_position_x')->nullable();
			$table->integer('web_position_y')->nullable();


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
		Schema::dropIfExists('screenshots');
	}
}
