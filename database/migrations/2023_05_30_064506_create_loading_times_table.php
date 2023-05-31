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
        Schema::create('loading_times', function (Blueprint $table) {
            $table->id();
			$table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
			$table->unsignedBigInteger('client_id')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients');
			$table->string('url');
			$table->unsignedBigInteger('loading_duration_raw');
			$table->unsignedBigInteger('loading_duration_fetched')->nullable();
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
        Schema::dropIfExists('loading_times');
    }
};
