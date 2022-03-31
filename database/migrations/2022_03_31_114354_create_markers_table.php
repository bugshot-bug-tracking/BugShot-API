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
        Schema::create('markers', function (Blueprint $table) {
            $table->uuid('id')->primary();

			$table->unsignedBigInteger('screenshot_id');
			$table->foreign('screenshot_id')->references('id')->on('screenshots')->onDelete('cascade');

			$table->float('position_x')->nullable();
			$table->float('position_y')->nullable();
			$table->float('web_position_x')->nullable();
			$table->float('web_position_y')->nullable();
            $table->float('target_x')->nullable();
            $table->float('target_y')->nullable();
            $table->float('target_height')->nullable();
            $table->float('target_width')->nullable();
            $table->float('scroll_x')->nullable();
            $table->float('scroll_y')->nullable();
            $table->float('screenshot_height')->nullable();
            $table->float('screenshot_width')->nullable();
            $table->text('target_full_selector')->nullable();
            $table->text('target_short_selector')->nullable();
            $table->text('target_html')->nullable();

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
        Schema::dropIfExists('markers');
    }
};
