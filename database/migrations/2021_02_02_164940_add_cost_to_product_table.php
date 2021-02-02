<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCostToProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->integer('department_id')->nullable()->after('branch_id');
            $table->integer('category_id')->nullable()->after('department_id');
            $table->decimal('cost')->nullable()->after('product_name');
            $table->decimal('reorder_level')->nullable()->after('quantity');
            $table->decimal('recommend_quantity')->nullable()->after('reorder_level');
            $table->string('unit_type')->nullable()->after('recommend_quantity');
            $table->dropColumn('quantity_sync');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('department_id');
            $table->dropColumn('category_id');
            $table->dropColumn('cost');
            $table->dropColumn('reorder_level');
            $table->dropColumn('recommend_quantity');
            $table->dropColumn('unit_type');
            $table->integer('quantity_sync')->nullable()->after('quantity');
        });
    }
}
