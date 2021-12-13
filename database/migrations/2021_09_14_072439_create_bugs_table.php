<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bugs', function (Blueprint $table) {
			$table->uuid('id')->primary();

			$table->string('project_id');
			$table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');

			$table->unsignedBigInteger('user_id')->nullable();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

			$table->string('status_id');
			$table->foreign('status_id')->references('id')->on('statuses')->onDelete('restrict');

			$table->unsignedBigInteger('priority_id');
			$table->foreign('priority_id')->references('id')->on('priorities')->onDelete('restrict');

			$table->string('designation');
			$table->text('description')->nullable();
			$table->text('url')->nullable();
			$table->string('operating_system')->nullable();
			$table->string('browser')->nullable();
			$table->text('selector')->nullable();
			$table->string('resolution')->nullable();
			$table->timestamp('deadline')->nullable();
			$table->smallInteger('order_number')->default(0);

			$table->timestamps();
			$table->timestamp('deleted_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bugs');
	}
}
