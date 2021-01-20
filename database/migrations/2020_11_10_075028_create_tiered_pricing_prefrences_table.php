<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTieredPricingPrefrencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tiered_pricing_prefrences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('global')->default(1);
            $table->text('users_id')->nullable();
            $table->text('stores_id')->nullable();
            $table->boolean('enabled')->default(1);
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
        Schema::dropIfExists('tiered_pricing_prefrences');
    }
}
