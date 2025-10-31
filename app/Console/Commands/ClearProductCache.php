<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearProductCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all product-related cache';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Clearing product cache...');

        // Clear product extra fields cache
        $this->clearCacheByPattern('product_extra_fields_*');

        // Clear products with characteristics cache
        $this->clearCacheByPattern('products_with_characteristics_*');

        // Clear child categories cache
        $this->clearCacheByPattern('child_categories_*');

        // Clear navigation categories cache
        Cache::forget('navigation_categories');

        // Clear manufacturers cache
        Cache::forget('manufacturers_with_counts');

        // Clear price range cache
        Cache::forget('price_range_global');

        $this->info('Product cache cleared successfully!');

        return 0;
    }

    /**
     * Clear cache by pattern
     *
     * @param string $pattern
     * @return void
     */
    private function clearCacheByPattern(string $pattern)
    {
        $keys = Cache::getRedis()->keys($pattern);

        if (!empty($keys)) {
            Cache::getRedis()->del($keys);
            $this->line("Cleared " . count($keys) . " cache entries matching pattern: {$pattern}");
        }
    }
}
