<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlterWholesalesToBranchProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_product', function (Blueprint $table) {
            $table->decimal('normal_wholesale_quantity',12,4)->change();
            $table->decimal('normal_wholesale_quantity2',12,4)->change();
            $table->decimal('normal_wholesale_quantity3',12,4)->change();
            $table->decimal('normal_wholesale_quantity4',12,4)->change();
            $table->decimal('normal_wholesale_quantity5',12,4)->change();
            $table->decimal('normal_wholesale_quantity6',12,4)->change();
            $table->decimal('normal_wholesale_quantity7',12,4)->change();
            $table->decimal('wholesale_quantity',12,4)->change();
            $table->decimal('wholesale_quantity2',12,4)->change();
            $table->decimal('wholesale_quantity3',12,4)->change();
            $table->decimal('wholesale_quantity4',12,4)->change();
            $table->decimal('wholesale_quantity5',12,4)->change();
            $table->decimal('wholesale_quantity6',12,4)->change();
            $table->decimal('wholesale_quantity7',12,4)->change();
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
            //
        });
    }
}
