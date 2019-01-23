<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopifyChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopify_plans', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->decimal('price', 6, 2)
                ->default('0000.00');
            $table->integer('trial_days')
                ->default('0');
            $table->boolean('active')
                ->default(true);

            $table->timestamps();
        });

        Schema::create('shopify_charges', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('shopify_shop_id');
            $table->string('charge_id');
            $table->string('name');
            $table->string('plan');
            $table->string('plan_id');
            $table->string('test')
                ->nullable();
            $table->integer('quantity');
            $table->integer('charge_type');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
            
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
        Schema::dropIfExists('shopify_plans');
        Schema::dropIfExists('shopify_charges');
    }
}
