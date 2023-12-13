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
        Schema::table('users', function (Blueprint $table) {
            $table->after('password', function ($table) {
                $table->string('subscription_item_id')->nullable();
                $table->foreign('subscription_item_id')->references('stripe_id')->on('subscription_items')->onDelete('set null');
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
        Schema::table('users', function (Blueprint $table) {
			$table->dropForeign('users_subscription_item_id_foreign');
			$table->dropColumn('subscription_item_id');
        });
    }
};
