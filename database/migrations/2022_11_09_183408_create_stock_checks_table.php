<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_checks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('destination');
            $table->integer('product_id');
            $table->string('barcode');
            $table->integer('user_id');
            $table->decimal('stock_count',15,4);
            $table->json('raw')->nullable();
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
        Schema::dropIfExists('stock_checks');
    }
}
