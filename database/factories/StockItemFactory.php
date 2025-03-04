<?php

namespace Database\Factories;

use App\Models\ItemCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => ItemCategory::factory(),
            'name' => $this->faker->word(),
            'price' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}
