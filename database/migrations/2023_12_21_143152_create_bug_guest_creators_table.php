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
		Schema::create('bug_guest_creators', function (Blueprint $table) {
			$table->id();
			$table->string('bug_id');
			$table->foreign('bug_id')->references('id')->on('bugs')->onDelete('cascade');
			$table->string("name")->nullable();
			$table->string("email")->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bug_guest_creators');
	}
};
