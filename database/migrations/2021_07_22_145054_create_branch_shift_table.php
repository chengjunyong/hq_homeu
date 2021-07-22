<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchShiftTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_shift', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('branch_opening_id')->nullable();
            $table->string('branch_id')->nullable();
            $table->string('ip')->nullable();
            $table->string('cashier_name')->nullable();
            $table->string('opening', 1)->nullable();
            $table->string('opening_by_name')->nullable();
            $table->double('opening_amount', 15, 2)->nullable();
            $table->dateTime('opening_date_time')->nullable();
            $table->string('closing', 1)->nullable();
            $table->string('closing_by_name')->nullable();
            $table->double('closing_amount', 15, 2)->nullable();
            $table->double('calculated_amount', 15, 2)->nullable();
            $table->double('diff', 15, 2)->nullable();
            $table->dateTime('closing_date_time')->nullable();
            $table->dateTime('shift_created_at')->nullable();
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
        Schema::dropIfExists('branch_shift');
    }
}
