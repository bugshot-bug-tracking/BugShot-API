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
		Schema::create('jira_bug_links', function (Blueprint $table) {
			$table->id();

			$table->foreignId('project_link_id')->constrained(
				table: 'jira_project_links',
			)->onDelete('cascade');

			$table->string('bug_id');
			$table->foreign('bug_id')->references('id')->on('bugs')->onDelete('cascade');

			$table->string('issue_id');
			$table->string('issue_key');
			$table->string('issue_url');

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
		Schema::dropIfExists('jira_bug_links');
	}
};
