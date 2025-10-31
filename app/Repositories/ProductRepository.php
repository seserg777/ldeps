<?php

namespace App\Repositories;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    /**
     * Get published products with relationships.
     *
     * @param array $filters
     * @return Builder
     */
    public function getPublishedProducts(array $filters = []): Builder
    {
        $query = Product::published()
            ->with(['categories', 'manufacturer']);

        $this->applyFilters($query, $filters);

        return $query;
    }

    /**
     * Get product by path.
     *
     * @param string $path
     * @return Product|null
     */
    public function getByPath(string $path): ?Product
    {
        $pathSegments = explode('/', $path);
        $productAlias = end($pathSegments);
        array_pop($pathSegments);
        $categoryPath = implode('/', $pathSegments);

        $locale = app()->getLocale();
        $localeMap = [
            'uk' => 'uk-UA',
            'ru' => 'ru-UA',
            'en' => 'en-GB'
        ];
        $dbLocale = $localeMap[$locale] ?? 'uk-UA';
        $aliasField = "alias_{$dbLocale}";

        $product = Product::published()
            ->where($aliasField, $productAlias)
            ->first();

        // Fallback to product ID if not found by alias
        if (!$product && is_numeric($productAlias)) {
            $product = Product::published()
                ->where('product_id', $productAlias)
                ->first();
        }

        return $product;
    }

    /**
     * Search products by query.
     *
     * @param string $query
     * @param int $limit
     * @return Collection
     */
    public function search(string $query, int $limit = 10): Collection
    {
        return Product::published()
            ->search($query)
            ->with('categories')
            ->select('product_id', 'name_ru-UA', 'name_uk-UA', 'name_en-GB', 'product_price', 'product_thumb_image', 'product_ean', 'manufacturer_code', 'alias_uk-UA', 'alias_ru-UA', 'alias_en-GB')
            ->limit($limit)
            ->get();
    }

    /**
     * Get products by category IDs.
     *
     * @param array $categoryIds
     * @param array $filters
     * @return Builder
     */
    public function getByCategoryIds(array $categoryIds, array $filters = []): Builder
    {
        $query = Product::published()
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('vjprf_jshopping_products_to_categories.category_id', $categoryIds);
            });

        $this->applyFilters($query, $filters);

        return $query;
    }

    /**
     * Apply filters to query.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applyFilters(Builder $query, array $filters): void
    {
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

        // Sort
        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $query->orderBy('product_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('product_price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name_uk-UA', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name_uk-UA', 'desc');
                    break;
                case 'newest':
                default:
                    $query->orderBy('product_id', 'desc');
                    break;
            }
        }
    }
}
