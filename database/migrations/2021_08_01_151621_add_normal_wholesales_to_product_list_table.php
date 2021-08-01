<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNormalWholesalesToProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_list', function (Blueprint $table) {
            $table->decimal('normal_wholesale_price',8,4)->nullable()->after('quantity');
            $table->decimal('normal_wholesale_price2',8,4)->nullable()->after('normal_wholesale_price');
            $table->decimal('normal_wholesale_quantity',8,2)->nullable()->after('normal_wholesale_price2');
            $table->decimal('normal_wholesale_quantity2',8,2)->nullable()->after('normal_wholesale_quantity');
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
            $table->dropColumn(['normal_wholesale_price','normal_wholesale_price2','normal_wholesale_quantity','normal_wholesale_quantity2']);
        });
    }
}
