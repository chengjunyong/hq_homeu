<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecimalToPurchaseOrderDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->string('measurement')->default('unit')->after('product_name');
            $table->decimal('cost',15,4)->change();
            $table->decimal('quantity',15,4)->change();
            $table->decimal('received',15,4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->dropColumn('measurement');
        });
    }
}
