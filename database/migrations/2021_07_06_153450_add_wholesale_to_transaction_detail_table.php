<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWholesaleToTransactionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_detail', function (Blueprint $table) {
            $table->integer('wholesale_quantity')->after('price')->nullable();
            $table->double('wholesale_price', 12, 2)->after('wholesale_quantity')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_detail', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'wholesale_quantity']);
        });
    }
}
