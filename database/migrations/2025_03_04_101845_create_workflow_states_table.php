<?php

use App\Traits\MigratesData;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkflowStatesTable extends Migration
{
    use MigratesData;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workflow_states', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('description')->nullable('false');
        });

        $this->migrateData(function () {
            DB::table('workflow_states')->insertMany([
                [
                    'id' => 1,
                    'description' => 'Level 1'
                ],
                [
                    'id' => 2,
                    'description' => 'Level 2'
                ]
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workflow_states');
    }
}
