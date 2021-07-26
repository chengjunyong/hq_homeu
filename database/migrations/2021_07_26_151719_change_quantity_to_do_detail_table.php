<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeQuantityToDoDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('do_detail', function (Blueprint $table) {
            $table->decimal('quantity')->change();
            $table->decimal('stock_lost_quantity')->change();
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
            $table->integer('quantity')->change();
            $table->integer('stock_lost_quantity')->change();
        });
    }
}
