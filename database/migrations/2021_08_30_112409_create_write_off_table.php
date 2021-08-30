<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWriteOffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('write_off', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('seq_no');
            $table->float('total_item',15,4)->nullable();
            $table->float('total_amount',20,4)->nullable();
            $table->string('created_by')->nullable();
            $table->datetime('write_off_date')->nullable();
            $table->integer('completed')->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('write_off');
    }
}
