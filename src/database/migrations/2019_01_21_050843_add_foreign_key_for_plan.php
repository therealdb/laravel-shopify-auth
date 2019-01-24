<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignKeyForPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopify_charges', function (Blueprint $table) {
            $table->foreign('plan_id')
                ->references('id')
                ->on('shopify_plans');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::table('shopify_charges', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
