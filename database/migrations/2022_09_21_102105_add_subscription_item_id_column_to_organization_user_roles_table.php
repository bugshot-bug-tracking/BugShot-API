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
        Schema::table('organization_user_roles', function (Blueprint $table) {
			$table->string('subscription_item_id')->nullable();
			$table->foreign('subscription_item_id')->references('stripe_id')->on('subscription_items')->onDelete('set null');
			$table->boolean('restricted_subscription_usage')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_user_roles', function (Blueprint $table) {
			$table->dropForeign('organization_user_roles_subscription_item_id_foreign');
			$table->dropColumn('subscription_item_id');
            $table->dropColumn('restricted_subscription_usage');
        });
    }
};
