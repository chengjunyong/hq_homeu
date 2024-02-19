<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrefillPromotionToProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_list', function (Blueprint $table) {
            $table->datetime('next_promotion_start')->nullable()->after('promotion_price');
            $table->datetime('next_promotion_end')->nullable()->after('next_promotion_start');
            $table->decimal('next_promotion_price','16','6')->nullable()->after('next_promotion_end');
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
            $table->dropColumn(['next_promotion_start','next_promotion_end','next_promotion_price']);
        });
    }
}
