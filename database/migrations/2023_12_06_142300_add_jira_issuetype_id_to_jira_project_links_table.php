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
		Schema::table('jira_project_links', function (Blueprint $table) {
			$table->after('jira_project_key', function ($table) {
				$table->string('jira_issuetype_id')->nullable();
			});
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('jira_project_links', function (Blueprint $table) {
			$table->dropColumn('jira_issuetype_id');
		});
	}
};
