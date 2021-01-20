<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWishlistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('product_name')->nullable();
            $table->double('cost')->nullable();
            $table->double('monthly_sales')->nullable();
            $table->text('description')->nullable();
            $table->text('reference')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->text('reject_reason')->nullable();
            $table->double('approved_price')->nullable();
            $table->unsignedBigInteger('related_product_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
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
        Schema::dropIfExists('wishlists');
    }
}
