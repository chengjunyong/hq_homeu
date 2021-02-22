<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColToDoDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('do_detail', function (Blueprint $table) {
            $table->integer('stock_lost_quantity')->nullable()->default(0)->after('quantity');
            $table->text('stock_lost_reason')->nullable()->after('stock_lost_quantity');
            $table->text('remark')->nullable()->after('stock_lost_reason');
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
            $table->dropColumn('stock_lost_quantity');
            $table->dropColumn('stock_lost_reason');
            $table->dropColumn('remark');
        });
    }
}
