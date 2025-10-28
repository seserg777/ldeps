<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Collection;

class SearchService
{
    /**
     * Perform global search across products, categories, and manufacturers.
     *
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function globalSearch(string $query, int $limit = 5): array
    {
        if (strlen($query) < 2) {
            return [
                'products' => collect(),
                'categories' => collect(),
                'manufacturers' => collect()
            ];
        }

        return [
            'products' => $this->searchProducts($query, $limit),
            'categories' => $this->searchCategories($query, $limit),
            'manufacturers' => $this->searchManufacturers($query, $limit)
        ];
    }

    /**
     * Search products.
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    private function searchProducts(string $query, int $limit): Collection
    {
        return Product::published()
            ->search($query)
            ->with('categories')
            ->select('product_id', 'name_ru-UA', 'name_uk-UA', 'name_en-GB', 'product_price', 'product_thumb_image', 'product_ean', 'manufacturer_code', 'alias_uk-UA', 'alias_ru-UA', 'alias_en-GB')
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->name,
                    'price' => $product->formatted_price,
                    'image' => $product->thumbnail_url,
                    'url' => route('products.show-by-path', $product->full_path),
                    'ean' => $product->product_ean,
                    'manufacturer_code' => $product->manufacturer_code,
                ];
            });
    }

    /**
     * Search categories.
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    private function searchCategories(string $query, int $limit): Collection
    {
        return Category::active()
            ->where(function($q) use ($query) {
                $q->where('name_uk-UA', 'like', "%{$query}%")
                  ->orWhere('name_ru-UA', 'like', "%{$query}%")
                  ->orWhere('name_en-GB', 'like', "%{$query}%")
                  ->orWhere('alias_uk-UA', 'like', "%{$query}%")
                  ->orWhere('alias_ru-UA', 'like', "%{$query}%")
                  ->orWhere('alias_en-GB', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->category_id,
                    'name' => $category->name,
                    'url' => route('category.show', $category->full_path),
                ];
            });
    }

    /**
     * Search manufacturers.
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    private function searchManufacturers(string $query, int $limit): Collection
    {
        return Manufacturer::published()
            ->where(function($q) use ($query) {
                $q->where('name_uk-UA', 'like', "%{$query}%")
                  ->orWhere('name_ru-UA', 'like', "%{$query}%")
                  ->orWhere('name_en-GB', 'like', "%{$query}%")
                  ->orWhere('code_1c', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get()
            ->map(function ($manufacturer) {
                return [
                    'id' => $manufacturer->manufacturer_id,
                    'name' => $manufacturer->name,
                    'url' => route('products.index', ['manufacturer' => $manufacturer->manufacturer_id]),
                ];
            });
    }
}
