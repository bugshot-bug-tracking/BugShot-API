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
        Schema::create('reports', function (Blueprint $table) {
            $table->uuid('id')->primary();

			$table->string('export_id');
			$table->foreign('export_id')->references('id')->on('exports')->onDelete('cascade');
			$table->unsignedBigInteger('generated_by')->nullable();
			$table->foreign('generated_by')->references('id')->on('users')->nullOnDelete();
			$table->text('url')->nullable();

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
        Schema::dropIfExists('reports');
    }
};
