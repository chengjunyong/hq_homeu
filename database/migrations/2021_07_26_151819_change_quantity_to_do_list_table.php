<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeQuantityToDoListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('do_list', function (Blueprint $table) {
            $table->decimal('total_item')->change();
            $table->decimal('stock_lost')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('do_list', function (Blueprint $table) {
            $table->integer('total_item')->change();
            $table->integer('stock_lost')->change();
        });
    }
}
