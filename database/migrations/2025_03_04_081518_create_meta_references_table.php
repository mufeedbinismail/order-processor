<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetaReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meta_references', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('system_type_id')->nullable(false);
            $table->string('template')->nullable(false);
            $table->bigInteger('next_seq_no')->nullable(false);
            $table->foreign('system_type_id')->references('id')->on('system_types');
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
        Schema::dropIfExists('meta_references');
    }
}
