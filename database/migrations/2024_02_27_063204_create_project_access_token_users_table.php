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
        Schema::create('project_access_token_users', function (Blueprint $table) {
			$table->string('pat_id');
			$table->string('project_id');
			$table->unsignedBigInteger('user_id');

			$table->primary(['pat_id', 'user_id']);

			$table->foreign('pat_id')->references('id')->on('project_access_tokens')->onDelete('cascade');
			$table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::dropIfExists('project_access_token_users');
    }
};
