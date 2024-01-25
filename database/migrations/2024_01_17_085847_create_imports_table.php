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
        Schema::create('imports', function (Blueprint $table) {
            $table->uuid();
			$table->unsignedBigInteger('status_id');
			$table->unsignedBigInteger('imported_by')->nullable();
			$table->foreign('imported_by')->references('id')->on('users')->nullOnDelete();
			$table->foreign('status_id')->references('id')->on('import_statuses')->onDelete('cascade');
			$table->text('source')->nullable();
			$table->text('target')->nullable();
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
        Schema::dropIfExists('imports');
    }
};
