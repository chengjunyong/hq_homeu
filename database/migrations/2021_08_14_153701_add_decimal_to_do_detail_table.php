<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecimalToDoDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('do_detail', function (Blueprint $table) {
            $table->decimal('price',15,3)->change();
            $table->decimal('cost',15,3)->change();
            $table->decimal('quantity',15,3)->change();
            $table->decimal('stock_lost_quantity',15,3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('do_detail', function (Blueprint $table) {
            //
        });
    }
}
