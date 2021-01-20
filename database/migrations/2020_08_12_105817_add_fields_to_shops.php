<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToShops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
           $table->text('location_id')->nullable();
        });

        Schema::table('retailer_products', function (Blueprint $table) {
            $table->text('inventory_item_id')->nullable();
        });

        Schema::table('retailer_product_variants', function (Blueprint $table) {
            $table->text('inventory_item_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            //
        });
    }
}
