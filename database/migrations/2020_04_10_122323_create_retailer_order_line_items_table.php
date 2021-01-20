<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailerOrderLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retailer_order_line_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('retailer_order_id')->nullable(); //link line item with order
            $table->text('retailer_product_variant_id')->nullable(); //link with retailer product variant
            $table->text('shopify_product_id')->nullable();
            $table->text('shopify_variant_id')->nullable();
            $table->text('title')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('variant_title')->nullable();
            $table->string('sku')->nullable();
            $table->string('vendor')->nullable();
            $table->string('price')->nullable();
            /*Fulfilled By WeFullFill or AliExpress*/
            $table->string('fulfilled_by')->nullable();

            $table->string('requires_shipping')->nullable();
            $table->boolean('taxable')->nullable();
            $table->string('name')->nullable();
            $table->text('properties')->nullable();
            $table->integer('fulfillable_quantity')->nullable();
            $table->string('fulfillment_status')->nullable();
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
        Schema::dropIfExists('retailer_order_line_items');
    }
}
