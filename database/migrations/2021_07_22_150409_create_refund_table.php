<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('refund', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('branch_id')->nullable();
            $table->integer('branch_refund_id')->nullable();
            $table->string('branch_opening_id')->nullable();
            $table->string('ip')->nullable();
            $table->string('cashier_name')->nullable();
            $table->string('created_by')->nullable();
            $table->double('subtotal', 15, 2)->nullable();
            $table->double('round_off', 15, 2)->nullable();
            $table->double('total', 15, 2)->nullable();
            $table->dateTime('refund_created_at')->nullable();
            $table->timestamps();
        });

        Schema::create('refund_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('branch_id')->nullable();
            $table->integer('branch_refund_detail_id')->nullable();
            $table->integer('branch_refund_id')->nullable();
            $table->string('department_id')->nullable();
            $table->string('category_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('barcode')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->double('price', 15, 2)->nullable();
            $table->double('subtotal', 15, 2)->nullable();
            $table->double('total', 15, 2)->nullable();
            $table->dateTime('refund_detail_created_at')->nullable();
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
        Schema::dropIfExists('refund');
        Schema::dropIfExists('refund_detail');
    }
}
