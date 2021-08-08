<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCostStructureToTmpInvoicePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tmp_invoice_purchase', function (Blueprint $table) {
          $table->decimal('total',15,4)->change();
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
