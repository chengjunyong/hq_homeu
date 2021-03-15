<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('supplier_code')->nullable();
            $table->text('supplier_name')->nullable();
            $table->text('contact_number')->nullable();
            $table->text('email')->nullable();
            $table->text('address1')->nullable();
            $table->text('address2')->nullable();
            $table->text('address3')->nullable();
            $table->text('address4')->nullable();
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
        Schema::dropIfExists('supplier');
    }
}
