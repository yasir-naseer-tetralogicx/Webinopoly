<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailerImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retailer_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('isV')->default(0);
            $table->text('shopify_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->text('image')->nullable();
            $table->unsignedInteger('user_id')->nullable();
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
        Schema::dropIfExists('retailer_images');
    }
}
