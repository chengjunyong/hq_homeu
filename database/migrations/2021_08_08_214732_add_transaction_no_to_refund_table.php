<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionNoToRefundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('refund', function (Blueprint $table) {
            $table->string('transaction_no')->after('created_by')->nullable();
        });

        Schema::table('refund_detail', function (Blueprint $table) {
            $table->string('transaction_no')->after('category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refund', function (Blueprint $table) {
            $table->dropColumn(['transaction_no']);
        });

        Schema::table('refund_detail', function (Blueprint $table) {
            $table->dropColumn(['transaction_no']);
        });
    }
}
