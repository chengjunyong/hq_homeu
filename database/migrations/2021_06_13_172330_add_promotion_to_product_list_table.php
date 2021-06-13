<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPromotionToProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_list', function (Blueprint $table) {
            $table->datetime('promotion_start')->after('schedule_price')->nullable();
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
        Schema::table('product_list', function (Blueprint $table) {
            $table->dropColumn(['promotion_start','promotion_end','promotion_price']);
        });
    }
}
