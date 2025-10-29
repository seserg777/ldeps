<?php

namespace App\Services\Product;

use App\Models\Product\Product;
use App\Models\Category\Category;
use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Get published products with filters.
     *
     * @param array $filters
     * @return Builder
     */
    public function getFilteredProducts(array $filters = []): Builder
    {
        $query = Product::published();

        // Price filter
        if (isset($filters['price_min']) && $filters['price_min']) {
            $query->where('product_price', '>=', $filters['price_min']);
        }
        if (isset($filters['price_max']) && $filters['price_max']) {
            $query->where('product_price', '<=', $filters['price_max']);
        }

        // Manufacturer filter
        if (isset($filters['manufacturer']) && !empty($filters['manufacturer'])) {
            $manufacturerIds = is_array($filters['manufacturer']) 
                ? $filters['manufacturer'] 
                : [$filters['manufacturer']];
            $query->whereIn('product_manufacturer_id', $manufacturerIds);
        }

        // Category filter
        if (isset($filters['category_ids']) && !empty($filters['category_ids'])) {
            $query->whereHas('categories', function($q) use ($filters) {
                $q->whereIn('vjprf_jshopping_products_to_categories.category_id', $filters['category_ids']);
            });
        }

        return $query;
    }

    /**
     * Search products by query.
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function searchProducts(string $query, int $limit = 10): Collection
    {
        return Product::published()
            ->search($query)
            ->with('categories')
            ->select('product_id', 'name_ru-UA', 'name_uk-UA', 'name_en-GB', 'product_price', 'product_thumb_image', 'product_ean', 'manufacturer_code', 'alias_uk-UA', 'alias_ru-UA', 'alias_en-GB')
            ->limit($limit)
            ->get();
    }

    /**
     * Get manufacturers for filter with product counts.
     *
     * @return Collection
     */
    public function getManufacturersForFilter(): Collection
    {
        return Manufacturer::published()
            ->ordered()
            ->withCount(['products' => function($query) {
                $query->published();
            }])
            ->having('products_count', '>', 0)
            ->get();
    }

    /**
     * Get price range for products.
     *
     * @param Builder $query
     * @return array
     */
    public function getPriceRange(Builder $query): array
    {
        $minPrice = $query->min('product_price') ?? 0;
        $maxPrice = $query->max('product_price') ?? 1000;

        return [
            'min' => $minPrice,
            'max' => $maxPrice
        ];
    }
}
