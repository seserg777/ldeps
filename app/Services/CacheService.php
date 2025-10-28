<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class CacheService
{
    /**
     * Cache TTL in seconds.
     */
    private const DEFAULT_TTL = 3600; // 1 hour

    /**
     * Get categories from cache or database.
     *
     * @return Collection
     */
    public function getCategories(): Collection
    {
        return Cache::remember('categories.navigation', self::DEFAULT_TTL, function () {
            return app(\App\Services\CategoryService::class)->getNavigationCategories();
        });
    }

    /**
     * Get manufacturers from cache or database.
     *
     * @return Collection
     */
    public function getManufacturers(): Collection
    {
        return Cache::remember('manufacturers.filter', self::DEFAULT_TTL, function () {
            return app(\App\Services\ProductService::class)->getManufacturersForFilter();
        });
    }

    /**
     * Get product by path from cache or database.
     *
     * @param string $path
     * @return mixed
     */
    public function getProductByPath(string $path)
    {
        return Cache::remember("product.path.{$path}", self::DEFAULT_TTL, function () use ($path) {
            return app(\App\Repositories\ProductRepository::class)->getByPath($path);
        });
    }

    /**
     * Clear all product-related cache.
     *
     * @return void
     */
    public function clearProductCache(): void
    {
        Cache::tags(['products', 'categories', 'manufacturers'])->flush();
    }

    /**
     * Clear specific cache by key.
     *
     * @param string $key
     * @return void
     */
    public function clearCache(string $key): void
    {
        Cache::forget($key);
    }

    /**
     * Get cache statistics.
     *
     * @return array
     */
    public function getCacheStats(): array
    {
        return [
            'driver' => config('cache.default'),
            'store' => config('cache.stores.' . config('cache.default')),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ];
    }
}
