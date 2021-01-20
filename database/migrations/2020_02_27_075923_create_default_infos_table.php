<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('ship_info')->nullable();
            $table->text('processing_time')->nullable();
            $table->text('ship_price')->nullable();
            $table->longText('warned_platform')->nullable();
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
        Schema::dropIfExists('default_infos');
    }
}
