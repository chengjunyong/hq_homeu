<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockLostHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_lost_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stock_lost_id')->nullable();
            $table->string('do_number')->nullable();
            $table->string('barcode')->nullable();
            $table->text('product_name')->nullable();
            $table->integer('lost_quantity')->nullable();
            $table->decimal('price_per_unit')->nullable();
            $table->decimal('total')->nullable();
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('stock_lost_history');
    }
}
