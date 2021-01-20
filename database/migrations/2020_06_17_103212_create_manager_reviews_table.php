<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManagerReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manager_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name')->nullable();
            $table->text('email')->nullable();
            $table->text('review')->nullable();
            $table->float('rating')->nullable();
            $table->text('attachment')->nullable();
            $table->unsignedInteger('ticket_id')->nullable();
            $table->unsignedInteger('manager_id')->nullable();
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
        Schema::dropIfExists('manager_reviews');
    }
}
