<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_purchase', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('invoice_date');
            $table->string('invoice_no');
            $table->integer('total_item');
            $table->double('total_cost');
            $table->integer('total_different_item');
            $table->integer('supplier_id');
            $table->string('supplier_name');
            $table->integer('creator_id');
            $table->string('creator_name');
            $table->integer('completed');
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
        Schema::dropIfExists('invoice_purchase');
    }
}
