<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationUserRolesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('organization_user_roles', function (Blueprint $table) {
			$table->string('organization_id');
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('role_id');

			$table->primary(['organization_id', 'user_id']);

			$table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
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
		Schema::dropIfExists('organization_user_roles');
	}
}
