<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasLocalizedFields;
use App\Models\Category\Category;
use App\Models\Manufacturer;
use App\Models\ProductCharacteristic;
use App\Models\ProductCategory;

class Product extends Model
{
    use HasFactory;
    use HasLocalizedFields;

    protected $table = 'vjprf_jshopping_products';
    protected $primaryKey = 'product_id';
    public $timestamps = false;

    /**
     * The attributes that should be hidden from serialization.
     *
     * @var array
     */
    protected $hidden = [
        'extra_fields'
    ];

    protected $fillable = [
        'product_id',
        'product_unit_quantity',
        'product_is_veto',
        'product_is_software',
        'product_qsign',
        'product_special_icons',
        'product_ean',
        'product_ean_copy',
        'manufacturer_code',
        'product_quantity',
        'product_unit',
        'unlimited',
        'product_availability',
        'product_date_added',
        'date_modify',
        'product_publish',
        'installment_plan',
        'product_tax_id',
        'product_template',
        'product_url',
        'product_old_price',
        'product_buy_price',
        'product_price',
        'retail_price',
        'special_price',
        'min_price',
        'different_prices',
        'product_weight',
        'product_thumb_image',
        'product_name_image',
        'product_full_image',
        'product_manufacturer_id',
        'product_is_add_price',
        'average_rating',
        'reviews_count',
        'delivery_times_id',
        'hits',
        'weight_volume_units',
        'basic_price_unit_id',
        'label_id',
        'vendor_id',

        // Multi-language fields - EN
        'name_en-GB',
        'alias_en-GB',
        'short_description_en-GB',
        'description_en-GB',
        'meta_title_en-GB',
        'meta_description_en-GB',
        'meta_keyword_en-GB',

        // Multi-language fields - UK
        'name_uk-UA',
        'alias_uk-UA',
        'short_description_uk-UA',
        'description_uk-UA',
        'meta_title_uk-UA',
        'meta_description_uk-UA',
        'meta_keyword_uk-UA',

        // Multi-language fields - RU
        'name_ru-UA',
        'alias_ru-UA',
        'short_description_ru-UA',
        'description_ru-UA',
        'meta_title_ru-UA',
        'meta_description_ru-UA',
        'meta_keyword_ru-UA',

        // Additional fields
        'image',
        'name_langmetadata',
        'alias_langmetadata',
        'short_description_langmetadata',
        'description_langmetadata',
        'meta_title_langmetadata',
        'meta_description_langmetadata',
        'meta_keyword_langmetadata',
        'parent_id',
        'currency_id',
        'access',
        'add_price_unit_id',

        // Auction fields
        'use_auction',
        'use_buy_now',
        'auction_start',
        'auction_end',
        'auction_finished',
        'auction_notify',
        'auction_price',
        'auction_id',
        'bid_increments',

        // Additional content fields
        'characteristics_en-GB',
        'delivery_kit_en-GB',
        'video_review_en-GB',
        'documentation_en-GB',
        'articles_en-GB',
        'characteristics_uk-UA',
        'delivery_kit_uk-UA',
        'video_review_uk-UA',
        'documentation_uk-UA',
        'articles_uk-UA',
        'characteristics_ru-UA',
        'delivery_kit_ru-UA',
        'video_review_ru-UA',
        'documentation_ru-UA',
        'articles_ru-UA',
        'full_description_en-GB',
        'full_description_uk-UA',
        'full_description_ru-UA',
        'hit_date',
        'start_hit_date',
        'hits_num',
        'calc_ru-UA',
        'calc_uk-UA',
        'calc_en-GB',
        'asset_id',
        'comingsoon_date',
        'addon_prod_availability_custom_status'
    ];

    /**
     * Scope for published products
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('product_publish', 1);
    }

    /**
     * Scope for search functionality
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name_ru-UA', 'like', "%{$search}%")
              ->orWhere('name_uk-UA', 'like', "%{$search}%")
              ->orWhere('name_en-GB', 'like', "%{$search}%")
              ->orWhere('product_ean', 'like', "%{$search}%")
              ->orWhere('product_ean_copy', 'like', "%{$search}%")
              ->orWhere('manufacturer_code', 'like', "%{$search}%")
              ->orWhere('short_description_ru-UA', 'like', "%{$search}%")
              ->orWhere('short_description_uk-UA', 'like', "%{$search}%")
              ->orWhere('short_description_en-GB', 'like', "%{$search}%")
              ->orWhere('full_description_ru-UA', 'like', "%{$search}%")
              ->orWhere('full_description_uk-UA', 'like', "%{$search}%")
              ->orWhere('full_description_en-GB', 'like', "%{$search}%")
              ->orWhere('characteristics_ru-UA', 'like', "%{$search}%")
              ->orWhere('characteristics_uk-UA', 'like', "%{$search}%")
              ->orWhere('characteristics_en-GB', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for price range filter
     */
    public function scopePriceRange(Builder $query, float $minPrice, float $maxPrice): Builder
    {
        return $query->whereBetween('product_price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope for manufacturer filter
     */
    public function scopeByManufacturer(Builder $query, int $manufacturerId): Builder
    {
        return $query->where('product_manufacturer_id', $manufacturerId);
    }

    /**
     * Scope for special price filter
     */
    public function scopeSpecialPrice(Builder $query): Builder
    {
        return $query->where('product_price', '<', 1000); // Example condition
    }

    /**
     * Scope for price ascending order
     */
    public function scopePriceAsc(Builder $query): Builder
    {
        return $query->orderBy('product_price', 'asc');
    }

    /**
     * Scope for price descending order
     */
    public function scopePriceDesc(Builder $query): Builder
    {
        return $query->orderBy('product_price', 'desc');
    }

    /**
     * Scope for popular products
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('hits', 'desc');
    }

    /**
     * Scope for rating order
     */
    public function scopeByRating(Builder $query): Builder
    {
        return $query->orderBy('hits', 'desc'); // Using hits as rating for now
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->product_price, 2) . ' ₴';
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): string
    {
        return $this->product_thumb_image ? asset('storage/' . $this->product_thumb_image) : asset('images/no-image.svg');
    }

    /**
     * Get product name based on locale
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();

        // Map locale to database field format
        $localeMap = [
            'uk' => 'uk-UA',
            'ru' => 'ru-UA',
            'en' => 'en-GB'
        ];

        $dbLocale = $localeMap[$locale] ?? 'uk-UA';
        $nameField = "name_{$dbLocale}";

        if (isset($this->attributes[$nameField]) && !empty($this->attributes[$nameField])) {
            return $this->attributes[$nameField];
        }

        // Fallback to Ukrainian name
        return $this->attributes['name_uk-UA'] ?? 'Без назви';
    }

    /**
     * Relationship with categories
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'vjprf_jshopping_products_to_categories', 'product_id', 'category_id');
    }

    /**
     * Relationship with product-category pivot table
     */
    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class, 'product_id', 'product_id');
    }

    /**
     * Relationship with manufacturer
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'product_manufacturer_id', 'manufacturer_id');
    }

    /**
     * Relationship with product characteristics
     */
    public function productCharacteristics()
    {
        return $this->hasMany(ProductCharacteristic::class, 'product_id', 'product_id');
    }

    /**
     * Get product attributes (variations).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productAttributes()
    {
        return $this->hasMany(\App\Models\ProductAttribute::class, 'product_id', 'product_id');
    }

    /**
     * Get "also bought" products through pivot table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function alsoBoughtProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'vjprf_jshopping_products_alsos',
            'product_id',
            'product_also_id',
            'product_id',
            'product_id'
        )->where('vjprf_jshopping_products.product_publish', 1);
    }

    /**
     * Get product extra fields as array with caching
     */
    public function getProductExtraFieldsAttribute(): array
    {
        // Force return empty array if this is called on a string
        if (is_string($this->attributes['extra_fields'] ?? null)) {
            \Log::info("Product {$this->product_id}: extra_fields is string, returning empty array");
            return [];
        }

        // Additional safety check - if this method returns a string, return empty array
        try {
            $result = $this->getCachedExtraFields();
            if (is_string($result)) {
                \Log::warning("Product {$this->product_id}: getCachedExtraFields returned string, returning empty array");
                return [];
            }
            return $result;
        } catch (\Exception $e) {
            \Log::error("Product {$this->product_id}: error in getProductExtraFieldsAttribute - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get cached extra fields
     */
    private function getCachedExtraFields(): array
    {
        $cacheKey = "product_extra_fields_{$this->product_id}";

        return \Cache::remember($cacheKey, 3600, function () {
            $extraFields = [];

            try {
                // Check if productCharacteristics relationship is loaded
                if (!$this->relationLoaded('productCharacteristics')) {
                    \Log::info("Product {$this->product_id}: productCharacteristics not loaded");
                    return $extraFields;
                }

                // Check if productCharacteristics exist and is a collection
                if (!$this->productCharacteristics || is_string($this->productCharacteristics)) {
                    \Log::info("Product {$this->product_id}: productCharacteristics is null or string");
                    return $extraFields;
                }

                // Additional check - ensure it's a collection
                if (!method_exists($this->productCharacteristics, 'count')) {
                    \Log::warning("Product {$this->product_id}: productCharacteristics is not a collection");
                    return $extraFields;
                }

                \Log::info("Product {$this->product_id}: processing " . $this->productCharacteristics->count() . " characteristics");

                foreach ($this->productCharacteristics as $characteristic) {
                    // Try to get field name and value
                    $fieldName = 'Unknown Field';
                    $fieldValue = 'Unknown Value';

                    if ($characteristic->extraField) {
                        $fieldName = $characteristic->extraField->name ?? 'Unknown Field';
                    }

                    if ($characteristic->extraFieldValue) {
                        $fieldValue = $characteristic->extraFieldValue->name ?? 'Unknown Value';
                    }

                    $extraFields[] = [
                        'field_name' => $fieldName,
                        'field_value' => $fieldValue,
                        'field_id' => $characteristic->extra_field ?? null,
                        'value_id' => $characteristic->extra_field_value ?? null
                    ];
                }

            } catch (\Exception $e) {
                \Log::error("Product {$this->product_id}: error in getCachedExtraFields - " . $e->getMessage());
            }

            \Log::info("Product {$this->product_id}: returning " . count($extraFields) . " extra fields");
            return $extraFields;
        });
    }

    /**
     * Clear cache for this product
     */
    public function clearCache()
    {
        \Cache::forget("product_extra_fields_{$this->product_id}");
    }

    /**
     * Boot method to clear cache on model events
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when product is updated
        static::updated(function ($product) {
            $product->clearCache();
        });

        // Clear cache when product is deleted
        static::deleted(function ($product) {
            $product->clearCache();
        });
    }

    /**
     * Get product image URL
     */
    public function getImageUrlAttribute()
    {
        if (!empty($this->product_thumb_image)) {
            return asset('storage/products/' . $this->product_thumb_image);
        }
        return asset('images/no-image.svg');
    }

    /**
     * Get short description
     */
    public function getShortDescriptionAttribute()
    {
        $locale = app()->getLocale();

        if ($locale === 'uk' && !empty($this->attributes['short_description_uk-UA'])) {
            return $this->attributes['short_description_uk-UA'];
        }

        if ($locale === 'ru' && !empty($this->attributes['short_description_ru-UA'])) {
            return $this->attributes['short_description_ru-UA'];
        }

        if ($locale === 'en' && !empty($this->attributes['short_description_en-GB'])) {
            return $this->attributes['short_description_en-GB'];
        }

        return $this->attributes['short_description_uk-UA'] ?? 'Опис відсутній';
    }

    /**
     * Get description
     */
    public function getDescriptionAttribute()
    {
        $locale = app()->getLocale();

        if ($locale === 'uk' && !empty($this->attributes['full_description_uk-UA'])) {
            return $this->attributes['full_description_uk-UA'];
        }

        if ($locale === 'ru' && !empty($this->attributes['full_description_ru-UA'])) {
            return $this->attributes['full_description_ru-UA'];
        }

        if ($locale === 'en' && !empty($this->attributes['full_description_en-GB'])) {
            return $this->attributes['full_description_en-GB'];
        }

        // Fallback to Ukrainian description
        return $this->attributes['full_description_uk-UA'] ?? 'Опис відсутній';
    }

    /**
     * Check if product has discount
     */
    public function hasDiscount()
    {
        return !empty($this->product_old_price) && $this->product_old_price > $this->product_price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentage()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        return round((($this->product_old_price - $this->product_price) / $this->product_old_price) * 100);
    }

    /**
     * Get formatted old price
     */
    public function getFormattedOldPriceAttribute()
    {
        if ($this->hasDiscount()) {
            return number_format($this->product_old_price, 0, ',', ' ') . ' ₴';
        }
        return null;
    }

    /**
     * Get average rating (placeholder)
     */
    public function getAverageRatingAttribute()
    {
        return 0; // Placeholder - implement rating system if needed
    }

    /**
     * Get reviews count (placeholder)
     */
    public function getReviewsCountAttribute()
    {
        return 0; // Placeholder - implement reviews system if needed
    }

    /**
     * Get characteristics
     */
    public function getCharacteristicsAttribute()
    {
        $locale = app()->getLocale();

        if ($locale === 'uk' && !empty($this->attributes['characteristics_uk-UA'])) {
            return $this->attributes['characteristics_uk-UA'];
        }

        if ($locale === 'ru' && !empty($this->attributes['characteristics_ru-UA'])) {
            return $this->attributes['characteristics_ru-UA'];
        }

        if ($locale === 'en' && !empty($this->attributes['characteristics_en-GB'])) {
            return $this->attributes['characteristics_en-GB'];
        }

        // Fallback to Russian characteristics
        return $this->attributes['characteristics_ru-UA'] ?? 'Характеристики не указаны';
    }

    /**
     * Get manufacturer name
     */
    public function getManufacturerNameAttribute(): string
    {
        if ($this->manufacturer) {
            return $this->manufacturer->name;
        }
        return 'Не вказано';
    }

    /**
     * Get product alias based on locale
     */
    public function getAliasAttribute(): string
    {
        $locale = app()->getLocale();

        // Map locale to database field format
        $localeMap = [
            'uk' => 'uk-UA',
            'ru' => 'ru-UA',
            'en' => 'en-GB'
        ];

        $dbLocale = $localeMap[$locale] ?? 'uk-UA';
        $aliasField = "alias_{$dbLocale}";

        if (isset($this->attributes[$aliasField]) && !empty($this->attributes[$aliasField])) {
            return $this->attributes[$aliasField];
        }

        // Fallback to product ID if no alias
        return (string) $this->product_id;
    }

    /**
     * Get full URL path for product
     */
    public function getFullPathAttribute(): string
    {
        $categoryPath = '';

        // Get the first category (products can belong to multiple categories)
        $category = $this->categories()->first();

        if ($category) {
            $categoryPath = $category->full_path;
        }

        $productAlias = $this->alias;

        return $categoryPath ? $categoryPath . '/' . $productAlias : $productAlias;
    }

    /**
     * Get base fields (non-localized) for Product model
     */
    protected static function getBaseFields(): array
    {
        return [
            'product_id',
            'product_unit_quantity',
            'product_is_veto',
            'product_is_software',
            'product_qsign',
            'product_special_icons',
            'product_ean',
            'product_ean_copy',
            'manufacturer_code',
            'product_quantity',
            'product_unit',
            'unlimited',
            'product_availability',
            'product_date_added',
            'date_modify',
            'product_publish',
            'installment_plan',
            'product_tax_id',
            'product_template',
            'product_url',
            'product_old_price',
            'product_buy_price',
            'product_price',
            'retail_price',
            'special_price',
            'min_price',
            'different_prices',
            'product_weight',
            'product_thumb_image',
            'product_name_image',
            'product_full_image',
            'product_manufacturer_id',
            'product_is_add_price',
            'average_rating',
            'reviews_count',
            'delivery_times_id',
            'hits',
            'weight_volume_units',
            'basic_price_unit_id',
            'label_id',
            'vendor_id',
            'image',
            'name_langmetadata',
            'alias_langmetadata',
            'short_description_langmetadata',
            'description_langmetadata',
            'meta_title_langmetadata',
            'meta_description_langmetadata',
            'meta_keyword_langmetadata',
            'parent_id',
            'currency_id',
            'access',
            'add_price_unit_id',
            'use_auction',
            'use_buy_now',
            'auction_start',
            'auction_end',
            'auction_finished',
            'auction_notify',
            'auction_price',
            'auction_id',
            'bid_increments',
            'hit_date',
            'start_hit_date',
            'hits_num',
            'asset_id',
            'comingsoon_date',
            'addon_prod_availability_custom_status'
        ];
    }
}
