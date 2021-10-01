<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invitations', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('target_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('comnpany_id')->references('id')->on('company')->onDelete('cascade');
			$table->foreign('project_id')->references('id')->on('project')->onDelete('cascade');
			$table->foreign('comnpany_role_id')->references('id')->on('role')->onDelete('cascade');
			$table->foreign('project_role_id')->references('id')->on('role')->onDelete('cascade');
			$table->foreign('status_id')->references('id')->on('invitation_statuses')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('invitations');
	}
}
