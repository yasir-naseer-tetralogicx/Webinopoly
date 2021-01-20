<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailerProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retailer_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->longText('description')->nullable();
            $table->text('type')->nullable();
            $table->text('vendor')->nullable();
            $table->text('tags')->nullable();
            $table->text('price')->nullable();
            $table->text('compare_price')->nullable();
            $table->text('cost')->nullable();
            $table->text('quantity')->nullable();
            $table->text('weight')->nullable();
            $table->text('sku')->nullable();
            $table->text('barcode')->nullable();
            $table->text('variants')->nullable();
            $table->text('status')->nullable();
            $table->text('fulfilled_by')->nullable();
            $table->boolean('toShopify')->default(0);
            $table->text('shopify_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('linked_product_id')->nullable();
            $table->unsignedInteger('shop_id')->nullable();
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
        Schema::dropIfExists('retailer_products');
    }
}
