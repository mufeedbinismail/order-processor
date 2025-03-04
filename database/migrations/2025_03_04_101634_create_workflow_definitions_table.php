<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_definitions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('workflow_id')->nullable(false);
            $table->int('previous_state_id')->nullable();
            $table->int('state_id')->nullable(false);
            $table->int('next_state_id')->nullable();
            $table->bigInteger('assigned_user_id')->nullable('false');
            $table->foreign('assigned_user_id')->references('users')->on('id');
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
        Schema::dropIfExists('workflow_definitions');
    }
}
