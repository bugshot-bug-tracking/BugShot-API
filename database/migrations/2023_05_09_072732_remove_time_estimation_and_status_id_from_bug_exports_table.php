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
        Schema::table('bug_exports', function (Blueprint $table) {
			$table->dropColumn('time_estimation');
			$table->dropForeign('bug_exports_status_id_foreign');
			$table->dropColumn('status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bug_exports', function (Blueprint $table) {
			$table->string("time_estimation");
			$table->string('status_id');
			$table->foreign('status_id')->references('id')->on('exported_bugs_statuses')->onDelete('cascade');
        });
    }
};
