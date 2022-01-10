<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachmentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attachments', function (Blueprint $table) {
			$table->id()->unique();

			$table->string('bug_id');
			$table->foreign('bug_id')->references('id')->on('bugs')->onDelete('cascade');

			$table->string('designation');
			$table->text('url');

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
		Schema::dropIfExists('attachments');
	}
}
