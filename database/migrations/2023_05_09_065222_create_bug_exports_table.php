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
        Schema::create('bug_exports', function (Blueprint $table) {

			$table->string('bug_id');
			$table->string('export_id');
			$table->string('status_id');
			$table->unsignedBigInteger('evaluated_by')->nullable();

			$table->primary(['bug_id', 'export_id']);

			$table->foreign('bug_id')->references('id')->on('bugs')->onDelete('cascade');
			$table->foreign('export_id')->references('id')->on('exports')->onDelete('cascade');
			$table->foreign('status_id')->references('id')->on('exported_bugs_statuses')->onDelete('cascade');
			$table->foreign('evaluated_by')->references('id')->on('users')->nullOnDelete()->nullable();

			$table->string("time_estimation");

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
        Schema::dropIfExists('bug_exports');
    }
};
