<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTmpGoodReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tmp_good_return', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('barcode');
            $table->string('product_name');
            $table->decimal('cost',8,4);
            $table->decimal('quantity',8,2);
            $table->decimal('total',8,4);
            $table->integer('user_id');
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
        Schema::dropIfExists('tmp_good_return');
    }
}
