<?php

namespace Database\Factories\Menu;

use App\Models\Menu\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'menutype' => $this->faker->randomElement(['mainmenu', 'topmenu', 'footermenu']),
            'title' => $this->faker->words(3, true),
            'alias' => $this->faker->slug(2),
            'note' => $this->faker->sentence(),
            'link' => $this->faker->url(),
            'type' => $this->faker->randomElement(['component', 'url', 'alias']),
            'published' => 1,
            'parent_id' => 1,
            'level' => 1,
            'ordering' => $this->faker->numberBetween(1, 100),
            'checked_out' => null,
            'checked_out_time' => null,
            'browserNav' => 0,
            'access' => 1,
            'img' => '',
            'template_style_id' => 0,
            'params' => '{}',
            'lft' => 0,
            'rgt' => 0,
            'home' => 0,
            'language' => '*',
            'client_id' => 0,
        ];
    }

    /**
     * Indicate that the menu item is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => 1,
        ]);
    }

    /**
     * Indicate that the menu item is a component link.
     */
    public function component(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'component',
            'link' => 'index.php?option=com_content&view=article&id=1',
        ]);
    }
}

