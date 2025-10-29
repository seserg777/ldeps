<?php

namespace Database\Factories;

use App\Models\Product\Product;
use App\Models\Category\Category;
use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 100, 10000);
        $oldPrice = $this->faker->boolean(30) ? $price * $this->faker->randomFloat(2, 1.1, 1.5) : 0;

        return [
            'name_uk-UA' => $this->faker->words(3, true),
            'name_ru-UA' => $this->faker->words(3, true),
            'name_en-GB' => $this->faker->words(3, true),
            'alias_uk-UA' => $this->faker->slug(2),
            'alias_ru-UA' => $this->faker->slug(2),
            'alias_en-GB' => $this->faker->slug(2),
            'product_price' => $price,
            'product_old_price' => $oldPrice,
            'product_quantity' => $this->faker->numberBetween(0, 100),
            'product_ean' => $this->faker->ean13(),
            'manufacturer_code' => $this->faker->bothify('MFG-###'),
            'product_manufacturer_id' => Manufacturer::factory(),
            'short_description_uk-UA' => $this->faker->sentence(10),
            'short_description_ru-UA' => $this->faker->sentence(10),
            'short_description_en-GB' => $this->faker->sentence(10),
            'full_description_uk-UA' => $this->faker->paragraphs(3, true),
            'full_description_ru-UA' => $this->faker->paragraphs(3, true),
            'full_description_en-GB' => $this->faker->paragraphs(3, true),
            'product_thumb_image' => $this->faker->imageUrl(300, 200, 'products'),
            'product_publish' => $this->faker->boolean(80),
            'product_rating' => $this->faker->randomFloat(1, 1, 5),
            'hits' => $this->faker->numberBetween(0, 1000),
        ];
    }

    /**
     * Indicate that the product is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_publish' => 1,
        ]);
    }

    /**
     * Indicate that the product is on sale.
     */
    public function onSale(): static
    {
        return $this->state(function (array $attributes) {
            $price = $attributes['product_price'] ?? $this->faker->randomFloat(2, 100, 1000);
            return [
                'product_old_price' => $price * $this->faker->randomFloat(2, 1.2, 2.0),
                'product_price' => $price,
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_quantity' => 0,
        ]);
    }
}
