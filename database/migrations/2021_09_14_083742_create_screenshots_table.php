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
			$table->id()->unique();

			$table->string('bug_id');
			$table->foreign('bug_id')->references('id')->on('bugs')->onDelete('cascade');

			$table->text('url');

			$table->integer('position_x')->nullable();
			$table->integer('position_y')->nullable();
			$table->integer('web_position_x')->nullable();
			$table->integer('web_position_y')->nullable();

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
		Schema::dropIfExists('screenshots');
	}
}
