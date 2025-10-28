<?php

namespace App\Listeners;

use App\Events\ProductViewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateProductViews implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductViewed $event): void
    {
        try {
            $event->product->increment('hits');
            
            Log::info('Product view updated', [
                'product_id' => $event->product->product_id,
                'hits' => $event->product->hits
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update product views', [
                'product_id' => $event->product->product_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
