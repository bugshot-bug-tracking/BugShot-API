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
		Schema::create('jira_project_links', function (Blueprint $table) {
			$table->id();

			$table->unsignedBigInteger('user_id');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

			$table->string('project_id');
			$table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

			$table->string('token_type');
			$table->text('access_token');
			$table->text('refresh_token');
			$table->integer('expires_in');
			$table->timestamp('expires_at');
			$table->string('scope');

			$table->string('site_id')->nullable();
			$table->string('site_name')->nullable();
			$table->string('site_url')->nullable();

			$table->string('jira_project_id')->nullable();
			$table->string('jira_project_name')->nullable();
			$table->string('jira_project_key')->nullable();

			$table->boolean("sync_bugs_to_jira")->default(false);
			$table->boolean("sync_bugs_from_jira")->default(false);
			$table->boolean("sync_comments_to_jira")->default(false);
			$table->boolean("sync_comments_from_jira")->default(false);
			$table->boolean("update_status_to_jira")->default(false);
			$table->boolean("update_status_from_jira")->default(false);


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
		Schema::dropIfExists('jira_project_links');
	}
};
