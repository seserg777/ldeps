<?php

namespace Database\Factories;

use App\Models\Module;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModuleFactory extends Factory
{
    protected $model = Module::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'note' => $this->faker->sentence(),
            'content' => $this->faker->paragraph(),
            'ordering' => $this->faker->numberBetween(0, 100),
            'position' => $this->faker->randomElement(['top', 'header', 'main', 'footer', 'bottom']),
            'published' => 1,
            'module' => $this->faker->randomElement(['mod_custom', 'mod_menu', 'mod_products', 'mod_search']),
            'access' => 1,
            'showtitle' => 1,
            'params' => '{}',
            'client_id' => 0,
            'language' => '*',
        ];
    }
}

