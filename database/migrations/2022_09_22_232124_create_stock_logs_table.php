<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('stock_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode')->nullable();
            $table->string('product_name')->nullable();
            $table->decimal('wb1_qty',30,6)->nullable();
            $table->decimal('wb2_qty',30,6)->nullable();
            $table->decimal('pm1_qty',30,6)->nullable();
            $table->decimal('pm2_qty',30,6)->nullable();
            $table->decimal('bachok_qty',30,6)->nullable();
            $table->decimal('pc_qty',30,6)->nullable();
            $table->decimal('warehouse_qty',30,6)->nullable();
            $table->date('batch')->nullable();
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
        Schema::connection('mysql2')->dropIfExists('stock_logs');
    }
}
