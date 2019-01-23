<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopifyChargesForeignKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shopify_charges', function (Blueprint $table) {
            $table->foreign('shopify_shop_id')
                ->references('id')
                ->on('shopify_shops');
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
            $table->dropForeign(['user_id']);
        });
        Schema::enableForeignKeyConstraints();
    }
}
