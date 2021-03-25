<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchStockHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_stock_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->nullable();
            $table->integer('branch_product_id')->nullable();
            $table->string('barcode')->nullable();
            $table->string('product_name')->nullable();
            $table->double('old_stock_count', 15, 2)->nullable();
            $table->double('new_stock_count', 15, 2)->nullable();
            $table->double('difference_count', 15, 2)->nullable();
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
        Schema::dropIfExists('branch_stock_history');
    }
}
