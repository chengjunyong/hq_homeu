<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDecimalFromGoodReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('good_return', function (Blueprint $table) {
            $table->decimal('total_quantity',15,4)->change();
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
        Schema::table('good_return', function (Blueprint $table) {
            //
        });
    }
}
