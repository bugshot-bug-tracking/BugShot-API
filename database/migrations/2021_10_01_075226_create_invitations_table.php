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
			$table->uuid('id')->primary();

			$table->unsignedBigInteger('sender_id');
			// $table->unsignedBigInteger('target_id');
			$table->string('target_email');
			$table->string("invitable_id");
			$table->string("invitable_type");
			$table->unsignedBigInteger("role_id");
			$table->unsignedBigInteger('status_id');

			$table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
			$table->foreign('status_id')->references('id')->on('invitation_statuses')->onDelete('cascade');

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
		Schema::dropIfExists('invitations');
	}
}
