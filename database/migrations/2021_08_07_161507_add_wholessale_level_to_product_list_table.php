<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWholessaleLevelToProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_list', function (Blueprint $table) {
            $table->decimal('normal_wholesale_price3',8,4)->after('normal_wholesale_price2')->nullable();
            $table->decimal('normal_wholesale_price4',8,4)->after('normal_wholesale_price3')->nullable();
            $table->decimal('normal_wholesale_price5',8,4)->after('normal_wholesale_price4')->nullable();
            $table->decimal('normal_wholesale_price6',8,4)->after('normal_wholesale_price5')->nullable();
            $table->decimal('normal_wholesale_price7',8,4)->after('normal_wholesale_price6')->nullable();
            $table->decimal('normal_wholesale_quantity3',8,2)->after('normal_wholesale_quantity2')->nullable();
            $table->decimal('normal_wholesale_quantity4',8,2)->after('normal_wholesale_quantity3')->nullable();
            $table->decimal('normal_wholesale_quantity5',8,2)->after('normal_wholesale_quantity4')->nullable();
            $table->decimal('normal_wholesale_quantity6',8,2)->after('normal_wholesale_quantity5')->nullable();
            $table->decimal('normal_wholesale_quantity7',8,2)->after('normal_wholesale_quantity6')->nullable();
            $table->decimal('wholesale_price3',8,4)->after('wholesale_price2')->nullable();
            $table->decimal('wholesale_price4',8,4)->after('wholesale_price3')->nullable();
            $table->decimal('wholesale_price5',8,4)->after('wholesale_price4')->nullable();
            $table->decimal('wholesale_price6',8,4)->after('wholesale_price5')->nullable();
            $table->decimal('wholesale_price7',8,4)->after('wholesale_price6')->nullable();
            $table->decimal('wholesale_quantity3',8,2)->after('wholesale_quantity2')->nullable();
            $table->decimal('wholesale_quantity4',8,2)->after('wholesale_quantity3')->nullable();
            $table->decimal('wholesale_quantity5',8,2)->after('wholesale_quantity4')->nullable();
            $table->decimal('wholesale_quantity6',8,2)->after('wholesale_quantity5')->nullable();
            $table->decimal('wholesale_quantity7',8,2)->after('wholesale_quantity6')->nullable();
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
            $table->dropColumn([
              'normal_wholesale_price3',
              'normal_wholesale_price4',
              'normal_wholesale_price5',
              'normal_wholesale_price6',
              'normal_wholesale_price7',
              'normal_wholesale_quantity3',
              'normal_wholesale_quantity4',
              'normal_wholesale_quantity5',
              'normal_wholesale_quantity6',
              'normal_wholesale_quantity7',
              'normal_wholesale_price3',
              'normal_wholesale_price4',
              'normal_wholesale_price5',
              'normal_wholesale_price6',
              'normal_wholesale_price7',
              'normal_wholesale_quantity3',
              'normal_wholesale_quantity4',
              'normal_wholesale_quantity5',
              'normal_wholesale_quantity6',
              'normal_wholesale_quantity7',
            ]);
        });
    }
}
