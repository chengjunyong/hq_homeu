<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWholesaleToProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_list', function (Blueprint $table) {
            $table->double('wholesale_price')->nullable()->after('promotion_price');
            $table->integer('wholesale_quantity')->nullable()->after('wholesale_price');
            $table->datetime('wholesale_start_date')->nullable()->after('wholesale_quantity');
            $table->datetime('wholesale_end_date')->nullable()->after('wholesale_start_date');
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
            $table->dropColumn(['wholesale_price','wholesale_quantity','wholesale_start_date','wholesales_end_date']);
        });
    }
}
