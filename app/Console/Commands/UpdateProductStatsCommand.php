<?php

namespace App\Console\Commands;

use App\Models\Product\Product;
use App\Jobs\UpdateProductStatistics;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateProductStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-statistics {--chunk=100 : Number of products to process at once}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update statistics for all products';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $chunkSize = (int) $this->option('chunk');
        
        $this->info('Starting product statistics update...');
        
        $totalProducts = Product::count();
        $this->info("Found {$totalProducts} products to process.");
        
        $bar = $this->output->createProgressBar($totalProducts);
        $bar->start();
        
        Product::chunk($chunkSize, function ($products) use ($bar) {
            foreach ($products as $product) {
                UpdateProductStatistics::dispatch($product->product_id);
                $bar->advance();
            }
        });
        
        $bar->finish();
        $this->newLine();
        
        $this->info('Product statistics update jobs have been queued.');
        Log::info('Product statistics update command completed', [
            'total_products' => $totalProducts,
            'chunk_size' => $chunkSize
        ]);
        
        return Command::SUCCESS;
    }
}
