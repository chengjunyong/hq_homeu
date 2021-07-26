<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeQuantityToBranchProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branch_product', function (Blueprint $table) {
            $table->decimal('quantity')->change();
            $table->decimal('wholesale_quantity')->change();
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
            $table->integer('quantity')->change();
            $table->integer('wholesale_quantity')->change();
        });
    }
}
