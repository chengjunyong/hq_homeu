<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToTransactionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_detail', function (Blueprint $table) {
            $table->index(['branch_transaction_id']);
            $table->index(['department_id']);
            $table->index(['category_id']);
            $table->index(['barcode']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_detail', function (Blueprint $table) {
            $table->dropIndex('transaction_detail_branch_transaction_id_index');
            $table->dropIndex('transaction_detail_department_id_index');
            $table->dropIndex('transaction_detail_category_id_index');
            $table->dropIndex('transaction_detail_barcode_index');
        });
    }
}
