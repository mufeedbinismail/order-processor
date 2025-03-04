<?php

use App\Models\ReferencePattern;
use App\Models\SystemType;
use App\Traits\MigratesData;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferencePatternsTable extends Migration
{
    use MigratesData;
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reference_patterns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('system_type_id')->nullable(false);
            $table->string('pattern')->nullable(false);
            $table->foreign('system_type_id')->references('id')->on('system_types');
            $table->unique(['system_type_id', 'pattern'], 'unique_system_type_pattern');
            $table->timestamps();
        });

        $this->migrateData(function () {
            ReferencePattern::create([
                'system_type_id' => SystemType::ORDER,
                'pattern' => ReferencePattern::getPatternString([
                    'ORD',
                    ReferencePattern::YEAR_TWO_DIGITS,
                    ReferencePattern::sequenceOf(5)
                ]),
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
        Schema::dropIfExists('reference_patterns');
    }
}
