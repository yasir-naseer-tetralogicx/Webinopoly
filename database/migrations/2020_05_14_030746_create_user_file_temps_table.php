<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFileTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_file_temps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('order_number')->nullable();
            $table->text('quantity')->nullable();
            $table->text('sku')->nullable();
            $table->text('name')->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->text('city')->nullable();
            $table->text('postcode')->nullable();
            $table->text('country')->nullable();
            $table->text('phone')->nullable();
            $table->text('email')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
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
        Schema::dropIfExists('user_file_temps');
    }
}
