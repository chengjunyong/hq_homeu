<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodReturnDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('good_return_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('good_return_id');
            $table->string('barcode');
            $table->string('product_name');
            $table->decimal('cost',8,4);
            $table->decimal('quantity',8,2);
            $table->decimal('total_cost',8,4);
            $table->string('update_by');
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
        Schema::dropIfExists('good_return_detail');
    }
}
