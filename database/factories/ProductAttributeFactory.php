<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeFactory extends Factory
{
    protected $model = ProductAttribute::class;

    public function definition(): array
    {
        return [
            'product_id' => 1,
            'retail_price' => $this->faker->randomFloat(2, 10, 1000),
            'buy_price' => $this->faker->randomFloat(2, 5, 500),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'special_price' => $this->faker->boolean(20),
            'unit' => $this->faker->numberBetween(1, 3),
            'count' => $this->faker->numberBetween(0, 100),
            'ean' => $this->faker->ean13(),
            'manufacturer_code' => $this->faker->bothify('??-####'),
            'weight' => $this->faker->randomFloat(4, 0.1, 50),
        ];
    }
}

