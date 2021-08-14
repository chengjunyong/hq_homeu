<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecimalToProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_list', function (Blueprint $table) {
            $table->decimal('cost',15,4)->change();
            $table->decimal('price',15,4)->change();
            $table->decimal('quantity',15,4)->change();
            $table->decimal('recommend_quantity',15,4)->change();
            $table->decimal('reorder_level',15,4)->change();
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
            //
        });
    }
}
