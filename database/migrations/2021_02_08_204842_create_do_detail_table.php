<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('do_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('do_number')->nullable();
            $table->integer('product_id')->nullable();
            $table->text('product_name')->nullable();
            $table->double('price')->nullable();
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('do_detail');
    }
}
