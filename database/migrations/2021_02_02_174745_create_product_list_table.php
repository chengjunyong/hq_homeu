<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('department_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('barcode')->nullable();
            $table->text('product_name')->nullable();
            $table->decimal('cost')->nullable();
            $table->decimal('price')->nullable();
            $table->decimal('quantity')->nullable();
            $table->decimal('reorder_level')->nullable();
            $table->decimal('recommend_quantity')->nullable();
            $table->string('unit_type')->nullable();
            $table->integer('product_sync')->nullable()->default(0);
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
        Schema::dropIfExists('product_list');
    }
}
