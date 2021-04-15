<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseRestockHistoryDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_restock_history_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('warehouse_history_id');
            $table->string('product_id');
            $table->string('barcode');
            $table->string('product_name');
            $table->decimal('cost');
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('warehouse_restock_history_detail');
    }
}
