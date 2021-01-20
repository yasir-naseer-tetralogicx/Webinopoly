<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminFileTempsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_file_temps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('order_name')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->text('tracking_company')->nullable();
            $table->text('tracking_number')->nullable();
            $table->text('tracking_url')->nullable();
            $table->text('tracking_notes')->nullable();
            $table->unsignedBigInteger('file_id')->nullable();
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
        Schema::dropIfExists('admin_file_temps');
    }
}
