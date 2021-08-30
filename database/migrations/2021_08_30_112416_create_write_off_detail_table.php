<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWriteOffDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('write_off_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('write_off_id')->nullable();
            $table->string('seq_no')->nullable();
            $table->string('barcode')->nullable();
            $table->string('product_name')->nullable();
            $table->float('quantity',12,4)->nullable();
            $table->float('cost',12,4)->nullable();
            $table->float('total',20,4)->nullable();
            $table->string('created_by')->nullable();
            $table->integer('completed')->default(0);
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
        Schema::dropIfExists('write_off_detail');
    }
}
