<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulerJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduler_job', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('branch_id')->nullable();
            $table->string('session_id');
            $table->string('entity_type');
            $table->string('entity_id');
            $table->longText('transaction')->nullable();
            $table->longText('transaction_detail')->nullable();
            $table->longText('records')->nullable();
            $table->boolean('sync')->nullable();
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
        Schema::dropIfExists('scheduler_job');
    }
}
