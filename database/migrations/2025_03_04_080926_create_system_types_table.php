<?php

use App\Models\SystemType;
use App\Traits\MigratesData;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntitiesTable extends Migration
{
    use MigratesData;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('system_types', function (Blueprint $table) {
            $table->integer('id')->nullable(false)->primary();
            $table->string('name')->nullable(false);
            $table->string('modal')->nullable(false);
            $table->timestamps();
        });

        $this->migrateData(function () {
            DB::table('system_types')->insertMany([
                ['id' => SystemType::USER, 'name' => 'User', 'modal' => 'App\Models\User'],
                ['id' => SystemType::CUSTOMER, 'name' => 'Customer', 'modal' => 'App\Models\Customer'],
                ['id' => SystemType::ORDER, 'name' => 'Order', 'modal' => 'App\Models\Order'],
                ['id' => SystemType::ITEM, 'name' => 'Item', 'modal' => 'App\Models\Item'],
                ['id' => SystemType::ITEM_CATEGORY, 'name' => 'Item Category', 'modal' => 'App\Models\ItemCategory']
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
        Schema::dropIfExists('entities');
    }
}
