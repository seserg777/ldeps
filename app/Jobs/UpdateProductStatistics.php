<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateProductStatistics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly int $productId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $product = Product::find($this->productId);
            
            if (!$product) {
                Log::warning('Product not found for statistics update', [
                    'product_id' => $this->productId
                ]);
                return;
            }

            // Update product statistics
            $this->updateProductStats($product);
            
            Log::info('Product statistics updated', [
                'product_id' => $this->productId
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update product statistics', [
                'product_id' => $this->productId,
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Update product statistics.
     *
     * @param Product $product
     * @return void
     */
    private function updateProductStats(Product $product): void
    {
        // Update view count, rating, etc.
        // This is a placeholder for actual statistics calculation
        $product->touch();
    }
}
