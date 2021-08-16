<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStructureFromStockLostHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_lost_history', function (Blueprint $table) {
            $table->string('product_name',191)->change();
            $table->decimal('lost_quantity',12,4)->change();
            $table->decimal('price_per_unit',12,4)->change();
            $table->decimal('cost',15,4)->change();
            $table->decimal('total',15,4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_lost_history', function (Blueprint $table) {
            //
        });
    }
}
