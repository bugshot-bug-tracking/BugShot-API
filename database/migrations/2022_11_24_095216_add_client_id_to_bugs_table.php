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
            $table->after('ai_id', function ($table) {
                $table->unsignedBigInteger('client_id')->nullable();
			    $table->foreign('client_id')->references('id')->on('clients');
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
			$table->dropForeign('bugs_client_id_foreign');
            $table->dropColumn('client_id');
        });
    }
};
