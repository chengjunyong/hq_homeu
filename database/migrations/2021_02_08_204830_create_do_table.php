<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('do', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('do_number')->nullable();
            $table->text('from')->nullable();
            $table->integer('from_branch_id')->nullable();
            $table->text('to')->nullable();
            $table->integer('to_branch_id')->nullable();
            $table->integer('total_item')->nullable();
            $table->integer('completed')->nullable();
            $table->integer('stock_lost_quantity')->nullable();
            $table->text('stock_lost_reason')->nullable();
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
        Schema::dropIfExists('do');
    }
}
