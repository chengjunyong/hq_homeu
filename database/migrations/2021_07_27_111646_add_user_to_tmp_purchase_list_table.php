<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserToTmpPurchaseListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tmp_purchase_list', function (Blueprint $table) {
            $table->integer('user_id')->nullable()->after('order_quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tmp_purchase_list', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}
