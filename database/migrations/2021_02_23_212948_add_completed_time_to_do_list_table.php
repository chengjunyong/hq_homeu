<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompletedTimeToDoListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('do_list', function (Blueprint $table) {
            $table->dateTime('completed_time')->after('completed')->nullable();
            $table->integer('stock_lost')->after('completed_time')->nullable();
            $table->dropColumn('stock_lost_quantity');
            $table->dropColumn('stock_lost_reason');
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
            $table->dropColumn('completed_time');
            $table->dropColumn('stock_lost');
            $table->integer('stock_lost_quantity')->nullable();
            $table->text('stock_lost_reason')->nullable();
        });
    }
}
