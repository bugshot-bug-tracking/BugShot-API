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
		Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('access_token');
        });

        Schema::create('project_access_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
			$table->char('access_token')->nullable();
			$table->text('description')->nullable();
			$table->string('project_id');
			$table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
			$table->unsignedBigInteger('user_id')->nullable();
			$table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
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
        Schema::dropIfExists('project_access_tokens');
    }
};
