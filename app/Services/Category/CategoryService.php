<?php

namespace App\Services\Category;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Get all root categories with their subcategories for navigation.
     *
     * @return Collection
     */
    public function getNavigationCategories(): Collection
    {
        return Category::active()
            ->root()
            ->ordered()
            ->withCount('products')
            ->with(['subcategories' => function ($query) {
                $query->active()->ordered()->with(['parent', 'subcategories' => function ($subQuery) {
                    $subQuery->active()->ordered();
                }]);
            }])
            ->get();
    }

    /**
     * Get category by path.
     *
     * @param string $path
     * @return Category
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getCategoryByPath(string $path): Category
    {
        $pathSegments = explode('/', $path);
        $lastSegment = end($pathSegments);

        return Category::active()
            ->where(function ($query) use ($lastSegment) {
                $query->where('alias_uk-UA', $lastSegment)
                      ->orWhere('alias_ru-UA', $lastSegment)
                      ->orWhere('alias_en-GB', $lastSegment);
            })
            ->firstOrFail();
    }

    /**
     * Get category and all its subcategory IDs recursively.
     *
     * @param Category $category
     * @return array
     */
    public function getCategoryAndSubcategoryIds(Category $category): array
    {
        $ids = [$category->category_id];

        $subcategories = Category::active()
            ->where('category_parent_id', $category->category_id)
            ->get();

        foreach ($subcategories as $subcategory) {
            $ids = array_merge($ids, $this->getCategoryAndSubcategoryIds($subcategory));
        }

        return $ids;
    }
}
