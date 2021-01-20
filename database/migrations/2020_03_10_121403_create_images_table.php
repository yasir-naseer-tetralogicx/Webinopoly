<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('isV')->default(0);
            $table->text('shopify_id')->nullable();
            $table->unsignedInteger('product_id')->nullable();
            $table->text('image')->nullable();
            $table->timestamps();
        });
        Schema::table('products', function($table) {
            $table->text('shopify_id')->nullable();
            $table->dropColumn('ship_info');
            $table->dropColumn('ship_processing_time');
            $table->dropColumn('ship_price');
            $table->dropColumn('warned_platform');
            $table->dropColumn('images');

        });
        Schema::table('product_variants', function($table) {
            $table->text('shopify_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
