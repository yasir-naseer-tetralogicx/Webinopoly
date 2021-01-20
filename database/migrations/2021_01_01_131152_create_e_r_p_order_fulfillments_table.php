<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateERPOrderFulfillmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_r_p_order_fulfillments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('retailer_order_id')->nullable();
            $table->text('logistic_code')->nullable();
            $table->text('track_number')->nullable();
            $table->text('logistic_name')->nullable();
            $table->text('track_url')->nullable();
            $table->text('erp_order_id')->nullable();
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
        Schema::dropIfExists('e_r_p_order_fulfillments');
    }
}
