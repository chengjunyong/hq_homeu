<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDecimalToInvoicePurchaseDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_purchase_detail', function (Blueprint $table) {
            $table->decimal('cost',15,4)->change();
            $table->decimal('quantity',15,4)->change();
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
        Schema::table('invoice_purchase_detail', function (Blueprint $table) {
            //
        });
    }
}
