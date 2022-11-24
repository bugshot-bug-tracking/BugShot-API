<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('designation');
            $table->text('description');

            $table->unsignedBigInteger('client_id')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unsignedBigInteger('version_type_id')->onDelete('cascade');
            $table->foreign('version_type_id')->references('id')->on('version_types');

            $table->boolean('supported');
            
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
        Schema::dropIfExists('versions');
    }
}
