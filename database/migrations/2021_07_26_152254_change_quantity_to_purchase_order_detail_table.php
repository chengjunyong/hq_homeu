<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeQuantityToPurchaseOrderDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchase_order_detail', function (Blueprint $table) {
            $table->decimal('quantity')->change();
            $table->decimal('received')->change();
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
            $table->integer('quantity')->change();
            $table->integer('received')->change();
        });
    }
}
