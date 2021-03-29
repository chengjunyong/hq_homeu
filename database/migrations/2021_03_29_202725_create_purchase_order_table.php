<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('po_number')->nullable()->unique();
            $table->string('supplier_id')->nullable();
            $table->string('supplier_code')->nullable();
            $table->text('supplier_name')->nullable();
            $table->integer('total_quantity_items')->nullable();
            $table->decimal('total_amount')->nullable();
            $table->integer('stock_lost')->nullable();
            $table->date('issue_date')->nullable();
            $table->integer("completed")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_order');
    }
}
