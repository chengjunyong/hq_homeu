<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpPurchaseList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_purchase_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('supplier_id');
            $table->text('supplier_name');
            $table->integer('warehouse_stock_id');
            $table->integer('department_id');
            $table->integer('category_id');
            $table->string('barcode');
            $table->text('product_name');
            $table->double('cost');
            $table->double('price');
            $table->integer('order_quantity');
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
        Schema::dropIfExists('tmp_purchase_list');
    }
}
