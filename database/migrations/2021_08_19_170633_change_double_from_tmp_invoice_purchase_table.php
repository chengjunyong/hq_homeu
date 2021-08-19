<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDoubleFromTmpInvoicePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tmp_invoice_purchase', function (Blueprint $table) {
            $table->decimal('cost',15,4)->change();
            $table->string('product_name',191)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tmp_invoice_purchase', function (Blueprint $table) {
            //
        });
    }
}
