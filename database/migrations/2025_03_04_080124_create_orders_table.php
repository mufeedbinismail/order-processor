<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id')->nullable(false);
            $table->date('order_date')->nullable(false);
            $table->string('reference')->nullable(false);
            $table->integer('version')->nullable(false);
            $table->float('total')->nullable(false);
            $table->boolean('is_active')->nullable(false)->default(true);
            $table->bigInteger('created_by')->nullable(false);
            $table->bigInteger('updated_by')->nullable(false);
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->unique(['reference', 'version']);
            $table->index('order_date');
            $table->index('total');
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
        Schema::dropIfExists('orders');
    }
}
