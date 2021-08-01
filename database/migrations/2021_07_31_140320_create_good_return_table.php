<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodReturnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('good_return', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('gr_no');
            $table->date('gr_date')->nullable();
            $table->string('ref_no')->nullable();
            $table->decimal('total_quantity',8,2);
            $table->decimal('total_cost',8,2);
            $table->integer('total_different_item');
            $table->integer('supplier_id');
            $table->string('supplier_name');
            $table->integer('creator_id');
            $table->string('creator_name');
            $table->timestamps();
            $table->softDeletes();
            $table->string('deleted_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('good_return');
    }
}
