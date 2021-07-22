<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashFloatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_float', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('branch_cash_float_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->string('created_by')->nullable();
            $table->string('ip')->nullable();
            $table->string('cashier_name')->nullable();
            $table->string('branch_opening_id')->nullable();
            $table->string('type')->nullable();
            $table->double('amount', 15, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->dateTime('cash_float_created_at')->nullable();
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
        Schema::dropIfExists('cash_float');
    }
}
