<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectUserRolesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_user_roles', function (Blueprint $table) {
			$table->unsignedBigInteger('project_id');
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('role_id');

			$table->primary(['project_id', 'user_id', 'role_id']);

			$table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
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
		Schema::dropIfExists('project_user_roles');
	}
}
