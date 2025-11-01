<?php

namespace Database\Factories\Category;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name_uk-UA' => $this->faker->words(2, true),
            'name_ru-UA' => $this->faker->words(2, true),
            'name_en-GB' => $this->faker->words(2, true),
            'alias' => $this->faker->slug(2),
            'alias_uk-UA' => $this->faker->slug(2),
            'alias_ru-UA' => $this->faker->slug(2),
            'alias_en-GB' => $this->faker->slug(2),
            'category_publish' => 1,
            'category_parent_id' => 0,
            'ordering' => $this->faker->numberBetween(1, 100),
            'category_image' => $this->faker->imageUrl(300, 200, 'categories'),
            'short_description_uk-UA' => $this->faker->sentence(),
            'short_description_ru-UA' => $this->faker->sentence(),
            'short_description_en-GB' => $this->faker->sentence(),
            'full_description_uk-UA' => $this->faker->paragraph(),
            'full_description_ru-UA' => $this->faker->paragraph(),
            'full_description_en-GB' => $this->faker->paragraph(),
            'meta_title_uk-UA' => $this->faker->words(5, true),
            'meta_title_ru-UA' => $this->faker->words(5, true),
            'meta_title_en-GB' => $this->faker->words(5, true),
            'meta_description_uk-UA' => $this->faker->sentence(),
            'meta_description_ru-UA' => $this->faker->sentence(),
            'meta_description_en-GB' => $this->faker->sentence(),
            'meta_keyword_uk-UA' => $this->faker->words(5, true),
            'meta_keyword_ru-UA' => $this->faker->words(5, true),
            'meta_keyword_en-GB' => $this->faker->words(5, true),
        ];
    }

    /**
     * Indicate that the category is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_publish' => 1,
        ]);
    }

    /**
     * Indicate that the category has a parent.
     */
    public function withParent(int $parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_parent_id' => $parentId,
        ]);
    }
}

