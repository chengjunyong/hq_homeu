<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('branch_id')->nullable();
            $table->integer('session_id')->nullable();
            $table->integer('branch_transaction_detail_id')->nullable();
            $table->integer('branch_transaction_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('barcode')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->double('price', 15, 2)->nullable();
            $table->double('discount', 15, 2)->nullable();
            $table->double('subtotal', 15, 2)->nullable();
            $table->double('total', 15, 2)->nullable();
            $table->integer('void')->nullable();
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
        Schema::dropIfExists('transaction_detail');
    }
}
