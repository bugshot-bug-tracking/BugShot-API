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
        Schema::table('markers', function (Blueprint $table) {
            $table->after('screenshot_width', function ($table) {
                $table->float('device_pixel_ratio')->nullable();
            });
        });

		Schema::table('screenshots', function (Blueprint $table) {
            $table->after('web_position_y', function ($table) {
                $table->float('device_pixel_ratio')->nullable();
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
        Schema::table('markers', function (Blueprint $table) {
            $table->dropColumn('device_pixel_ratio');
        });

		Schema::table('screenshots', function (Blueprint $table) {
            $table->dropColumn('device_pixel_ratio');
        });
    }
};
