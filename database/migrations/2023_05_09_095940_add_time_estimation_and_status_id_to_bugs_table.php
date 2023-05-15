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
        Schema::table('bugs', function (Blueprint $table) {
            $table->after('url', function ($table) {
			    $table->string('time_estimation')->nullable();
				$table->string('approval_status_id')->nullable();
				$table->foreign('approval_status_id')->references('id')->on('exported_bugs_statuses')->onDelete('cascade');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bugs', function (Blueprint $table) {
			$table->string('status_id');
            $table->dropColumn('time_estimation');
        });
    }
};
