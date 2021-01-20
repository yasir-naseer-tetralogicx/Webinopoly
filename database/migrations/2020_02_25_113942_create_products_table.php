<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->longText('description')->nullable();
            $table->longText('images')->nullable();
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
            $table->text('ship_info')->nullable();
            $table->text('ship_processing_time')->nullable();
            $table->text('ship_price')->nullable();
            $table->longText('warned_platform')->nullable();
            $table->text('category')->nullable();
            $table->text('sub_category')->nullable();
            $table->text('variants')->nullable();
            $table->text('status')->nullable();
            $table->text('managed_by')->nullable();
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
        Schema::dropIfExists('products');
    }
}
