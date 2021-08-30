<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('branch_delivery_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->integer('session_id')->nullable();
            $table->integer('opening_id')->nullable();
            $table->string('ip')->nullable();
            $table->string('cashier_name')->nullable();
            $table->string('transaction_no')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->decimal('subtotal', 15, 2)->nullable();
            $table->decimal('total_discount', 15, 2)->nullable();
            $table->string('voucher_code')->nullable();
            $table->string('delivery_type')->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->decimal('round_off', 15, 2)->nullable();
            $table->integer('completed')->nullable();
            $table->string('completed_by')->nullable();
            $table->dateTime('delivery_created_at')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('branch_id')->nullable();
            $table->integer('branch_delivery_detail_id')->nullable();
            $table->integer('branch_delivery_id')->nullable();
            $table->string('department_id')->nullable();
            $table->string('category_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('barcode')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('measurement_type')->nullable();
            $table->decimal('measurement', 12, 4)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('wholesale_price', 12, 4)->nullable();
            $table->decimal('discount', 12, 4)->nullable();
            $table->decimal('subtotal', 12, 4)->nullable();
            $table->decimal('total', 12, 4)->nullable();
            $table->dateTime('delivery_detail_created_at')->nullable();
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
        Schema::dropIfExists('delivery');
        Schema::dropIfExists('delivery_detail');
    }
}
