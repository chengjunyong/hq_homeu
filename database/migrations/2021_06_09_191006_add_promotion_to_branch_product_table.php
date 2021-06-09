<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionToBranchProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_product', function (Blueprint $table) {
            $table->datetime('promotion_start')->after('unit_type')->nullable();
            $table->datetime('promotion_end')->after('promotion_start')->nullable();
            $table->decimal('promotion_price')->after('promotion_end')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branch_product', function (Blueprint $table) {
            $table->dropColumn(['promotion_start','promotion_end','promotion_price']);
        });
    }
}
