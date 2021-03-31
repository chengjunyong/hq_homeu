<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchInfoToBranchStockHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_stock_history', function (Blueprint $table) {
            $table->string('user_name')->after('user_id');
            $table->integer('branch_id')->after('user_name');
            $table->string('branch_token')->after('branch_id');
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
            $table->dropColumn(['user_name', 'branch_id', 'branch_token']);
        });
    }
}
