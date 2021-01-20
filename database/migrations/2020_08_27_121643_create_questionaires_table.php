<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionaires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('gender')->nullable();
            $table->text('dob')->nullable();
            $table->text('new_to_business')->nullable();
            $table->text('product_ranges')->nullable();
            $table->text('countries')->nullable();
            $table->text('delivery_time')->nullable();
            $table->text('concerns')->nullable();
            $table->unsignedInteger('shop_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
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
        Schema::dropIfExists('questionaires');
    }
}
