<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToWalletRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wallet_requests', function (Blueprint $table) {

            $table->text('bank_name')->nullable()->change();
            $table->text('cheque_title')->nullable()->change();
            $table->text('cheque')->nullable()->change();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wallet_requests', function (Blueprint $table) {
            //
        });
    }
}
