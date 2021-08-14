<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMeasurementToWarehouseRestockHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouse_restock_history_detail', function (Blueprint $table) {
            $table->decimal('cost',15,4)->change();
            $table->decimal('quantity',15,4)->change();
            $table->string('measurement')->default('unit')->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warehouse_restock_history_detail', function (Blueprint $table) {
            $table->dropColumn('measurement');
        });
    }
}
