<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseRestockHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_restock_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('po_id');
            $table->string('po_number');
            $table->string('supplier_id');
            $table->string('supplier_name');
            $table->string('supplier_code');
            $table->string('invoice_number');
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
        Schema::dropIfExists('warehouse_restock_history');
    }
}
