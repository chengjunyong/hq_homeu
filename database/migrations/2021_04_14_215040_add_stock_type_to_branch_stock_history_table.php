<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockTypeToBranchStockHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_stock_history', function (Blueprint $table) {
            $table->string('stock_type')->after('user_name')->nullable();

            $table->string('user_name')->nullable()->change();
            $table->integer('branch_id')->nullable()->change();
            $table->string('branch_token')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_stock_history', function (Blueprint $table) {
            $table->dropColumn(['stock_type']);
        });
    }
}
