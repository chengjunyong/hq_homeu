<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentIdAndCategoryIdToBranchStockHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_stock_history', function (Blueprint $table) {
            $table->integer('department_id')->after('branch_product_id')->nullable();
            $table->integer('category_id')->after('department_id')->nullable();
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
            $table->dropColumn(['department_id', 'category_id']);
        });
    }
}
