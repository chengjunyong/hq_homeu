<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpOrderListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_order_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('branch_product_id');
            $table->integer('department_id');
            $table->integer('category_id');
            $table->string('barcode');
            $table->text('product_name');
            $table->decimal('cost')->nullable();
            $table->decimal('price')->nullable();
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
        Schema::dropIfExists('tmp_order_list');
    }
}
