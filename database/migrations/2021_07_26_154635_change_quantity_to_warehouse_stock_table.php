<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeQuantityToWarehouseStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->decimal('quantity')->change();
            $table->decimal('reorder_level')->change();
            $table->decimal('reorder_quantity')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->integer('quantity')->change();
            $table->integer('reorder_level')->change();
            $table->integer('reorder_quantity')->change();
        });
    }
}
