<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ManufacturerFactory extends Factory
{
    protected $model = Manufacturer::class;

    public function definition(): array
    {
        return [
            'name_uk-UA' => $this->faker->company(),
            'name_ru-UA' => $this->faker->company(),
            'name_en-GB' => $this->faker->company(),
            'alias_uk-UA' => $this->faker->slug(2),
            'alias_ru-UA' => $this->faker->slug(2),
            'alias_en-GB' => $this->faker->slug(2),
            'description_uk-UA' => $this->faker->paragraph(),
            'description_ru-UA' => $this->faker->paragraph(),
            'description_en-GB' => $this->faker->paragraph(),
            'manufacturer_logo' => $this->faker->imageUrl(200, 100, 'manufacturers'),
            'manufacturer_publish' => 1,
            'ordering' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the manufacturer is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'manufacturer_publish' => 1,
        ]);
    }
}

