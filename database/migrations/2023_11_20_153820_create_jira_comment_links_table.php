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
		Schema::create('jira_comment_links', function (Blueprint $table) {
			$table->id();

			$table->string('comment_id');
			$table->foreign('comment_id')->references('id')->on('comments')->onDelete('cascade');

			$table->string('jira_comment_id');
			$table->string('jira_comment_url');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('jira_comment_links');
	}
};
