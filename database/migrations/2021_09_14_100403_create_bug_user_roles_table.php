<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugUserRolesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bug_user_roles', function (Blueprint $table) {
			$table->string('bug_id');
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('role_id');

			$table->primary(['bug_id', 'user_id']);

			$table->foreign('bug_id')->references('id')->on('bugs')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bug_user_roles');
	}
}
