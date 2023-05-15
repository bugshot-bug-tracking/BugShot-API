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
        Schema::create('exports', function (Blueprint $table) {
            $table->uuid('id')->primary();

			$table->string('project_id');
			$table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
			$table->unsignedBigInteger('exported_by')->nullable();
			$table->foreign('exported_by')->references('id')->on('users')->nullOnDelete();

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
        Schema::dropIfExists('exports');
    }
};
