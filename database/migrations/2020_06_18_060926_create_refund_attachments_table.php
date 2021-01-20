<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund_attachments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('source')->nullable();
            $table->unsignedBigInteger('refund_id')->nullable();
            $table->unsignedBigInteger('thread_id')->nullable();
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
        Schema::dropIfExists('refund_attachments');
    }
}
