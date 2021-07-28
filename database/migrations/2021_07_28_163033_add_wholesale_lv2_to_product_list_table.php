<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWholesaleLv2ToProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_list', function (Blueprint $table) {
            $table->decimal('wholesale_price2',12,4)->nullable()->after('wholesale_price');
            $table->decimal('wholesale_quantity2',8,2)->nullable()->after('wholesale_quantity');
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
            $table->dropColumn(['wholesale_price2','wholesale_quantity2']);
        });
    }
}
