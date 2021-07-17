<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdateByToInvoicePurchaseDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_purchase_detail', function (Blueprint $table) {
            $table->decimal('total_cost')->after('quantity');
            $table->string('update_by')->after('total_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_purchase_detail', function (Blueprint $table) {
            $table->dropColumn(['update_by','total_cost']);
        });
    }
}
