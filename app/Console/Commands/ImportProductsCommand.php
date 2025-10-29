<?php

namespace App\Console\Commands;

use App\Models\Product\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from SQL file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("File {$file} not found!");
            return 1;
        }

        $this->info("Starting import from {$file}...");

        // Read SQL file
        $sql = file_get_contents($file);
        
        // Extract INSERT statements
        preg_match_all('/INSERT INTO `[^`]+` VALUES \((.*?)\);/s', $sql, $matches);
        
        if (empty($matches[1])) {
            $this->error("No INSERT statements found in the file!");
            return 1;
        }

        $this->info("Found " . count($matches[1]) . " INSERT statements");
        
        $progressBar = $this->output->createProgressBar(count($matches[1]));
        $progressBar->start();

        $imported = 0;
        $errors = 0;

        foreach ($matches[1] as $valuesString) {
            try {
                // Parse values from INSERT statement
                $values = $this->parseInsertValues($valuesString);
                
                if (empty($values)) {
                    continue;
                }

                // Create product data array
                $productData = $this->mapValuesToFields($values);
                
                // Insert into database
                DB::table('products')->insert($productData);
                $imported++;
                
            } catch (\Exception $e) {
                $errors++;
                $this->warn("Error importing row: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Import completed!");
        $this->info("Imported: {$imported} products");
        if ($errors > 0) {
            $this->warn("Errors: {$errors}");
        }

        return 0;
    }

    /**
     * Parse values from INSERT statement.
     *
     * @param string $valuesString
     * @return array
     */
    private function parseInsertValues(string $valuesString): array
    {
        $values = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = null;
        $escapeNext = false;

        for ($i = 0; $i < strlen($valuesString); $i++) {
            $char = $valuesString[$i];

            if ($escapeNext) {
                $current .= $char;
                $escapeNext = false;
                continue;
            }

            if ($char === '\\') {
                $escapeNext = true;
                $current .= $char;
                continue;
            }

            if (($char === "'" || $char === '"') && !$inQuotes) {
                $inQuotes = true;
                $quoteChar = $char;
                $current .= $char;
                continue;
            }

            if ($char === $quoteChar && $inQuotes) {
                $inQuotes = false;
                $quoteChar = null;
                $current .= $char;
                continue;
            }

            if ($char === ',' && !$inQuotes) {
                $values[] = trim($current);
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (!empty(trim($current))) {
            $values[] = trim($current);
        }

        return $values;
    }

    /**
     * Map parsed values to database fields.
     *
     * @param array $values
     * @return array
     */
    private function mapValuesToFields(array $values): array
    {
        // This is a simplified mapping - you may need to adjust based on your SQL structure
        $fields = [
            'product_unit_quantity', 'product_id', 'product_is_veto', 'product_is_software', 'product_qsign',
            'product_special_icons', 'product_ean', 'product_ean_copy', 'manufacturer_code', 'product_quantity',
            'product_unit', 'unlimited', 'product_availability', 'product_date_added', 'date_modify',
            'product_publish', 'installment_plan', 'product_tax_id', 'product_template', 'product_url',
            'product_old_price', 'product_buy_price', 'product_price', 'retail_price', 'special_price',
            'min_price', 'different_prices', 'product_weight', 'product_thumb_image', 'product_name_image',
            'product_full_image', 'product_manufacturer_id', 'product_is_add_price', 'average_rating',
            'reviews_count', 'delivery_times_id', 'hits', 'weight_volume_units', 'basic_price_unit_id',
            'label_id', 'vendor_id', 'name_en-GB', 'alias_en-GB', 'short_description_en-GB', 'description_en-GB',
            'meta_title_en-GB', 'meta_description_en-GB', 'meta_keyword_en-GB', 'parent_id', 'currency_id',
            'access', 'add_price_unit_id', 'name_uk-UA', 'alias_uk-UA', 'short_description_uk-UA', 'description_uk-UA',
            'meta_title_uk-UA', 'meta_description_uk-UA', 'meta_keyword_uk-UA', 'image', 'name_langmetadata',
            'alias_langmetadata', 'short_description_langmetadata', 'description_langmetadata', 'meta_title_langmetadata',
            'meta_description_langmetadata', 'meta_keyword_langmetadata', 'name_ru-UA', 'alias_ru-UA', 'short_description_ru-UA',
            'description_ru-UA', 'meta_title_ru-UA', 'meta_description_ru-UA', 'meta_keyword_ru-UA', 'name_ru-UA',
            'alias_ru-UA', 'short_description_ru-UA', 'description_ru-UA', 'meta_title_ru-UA', 'meta_description_ru-UA',
            'meta_keyword_ru-UA', 'use_auction', 'use_buy_now', 'auction_start', 'auction_end', 'auction_finished',
            'auction_notify', 'auction_price', 'auction_id', 'bid_increments', 'characteristics_en-GB',
            'delivery_kit_en-GB', 'video_review_en-GB', 'documentation_en-GB', 'articles_en-GB', 'characteristics_uk-UA',
            'delivery_kit_uk-UA', 'video_review_uk-UA', 'documentation_uk-UA', 'articles_uk-UA', 'characteristics_ru-UA',
            'delivery_kit_ru-UA', 'video_review_ru-UA', 'documentation_ru-UA', 'articles_ru-UA', 'full_description_en-GB',
            'full_description_uk-UA', 'full_description_ru-UA', 'hit_date', 'start_hit_date', 'hits_num',
            'calc_ru-UA', 'calc_uk-UA', 'calc_en-GB', 'asset_id', 'comingsoon_date', 'addon_prod_availability_custom_status'
        ];

        $data = [];
        foreach ($fields as $index => $field) {
            if (isset($values[$index])) {
                $value = trim($values[$index], "'\"");
                
                // Handle NULL values
                if ($value === 'NULL' || $value === '') {
                    $value = null;
                }
                
                // Handle datetime fields
                if (in_array($field, ['product_date_added', 'date_modify', 'auction_start', 'auction_end', 'comingsoon_date']) && $value) {
                    if ($value === '0000-00-00 00:00:00') {
                        $value = null;
                    }
                }
                
                // Handle date fields
                if (in_array($field, ['hit_date', 'start_hit_date']) && $value) {
                    if ($value === '0000-00-00') {
                        $value = null;
                    }
                }
                
                $data[$field] = $value;
            }
        }

        // Add timestamps
        $data['created_at'] = now();
        $data['updated_at'] = now();

        return $data;
    }
}
