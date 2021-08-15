<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecimalToGoodReturnDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('good_return_detail', function (Blueprint $table) {
            $table->decimal('cost',15,4)->change();
            $table->decimal('quantity',12,4)->change();
            $table->decimal('total_cost',15,4)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('good_return_detail', function (Blueprint $table) {
            //
        });
    }
}
