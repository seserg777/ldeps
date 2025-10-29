<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasLocalizedFields;
use App\Models\Product\Product;

class Manufacturer extends Model
{
    use HasFactory, HasLocalizedFields;

    protected $table = 'vjprf_jshopping_manufacturers';
    protected $primaryKey = 'manufacturer_id';
    public $timestamps = false;

    protected $fillable = [
        'manufacturer_id',
        'code_1c',
        'manufacturer_url',
        'manufacturer_logo',
        'additional_image',
        'manufacturer_publish',
        'products_page',
        'products_row',
        'ordering',
        'name_en-GB',
        'alias_en-GB',
        'short_description_en-GB',
        'description_en-GB',
        'meta_title_en-GB',
        'meta_description_en-GB',
        'meta_keyword_en-GB',
        'name_uk-UA',
        'alias_uk-UA',
        'short_description_uk-UA',
        'description_uk-UA',
        'meta_title_uk-UA',
        'meta_description_uk-UA',
        'meta_keyword_uk-UA',
        'name_ru-UA',
        'alias_ru-UA',
        'short_description_ru-UA',
        'description_ru-UA',
        'meta_title_ru-UA',
        'meta_description_ru-UA',
        'meta_keyword_ru-UA',
        'manufacturer_status',
        'name_langmetadata',
        'alias_langmetadata',
        'short_description_langmetadata',
        'description_langmetadata'
    ];

    /**
     * Scope for published manufacturers
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('manufacturer_publish', 1);
    }

    /**
     * Scope for manufacturers with special status (for icons)
     */
    public function scopeWithSpecialStatus(Builder $query): Builder
    {
        return $query->where('manufacturer_status', 1);
    }

    /**
     * Scope for ordering
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('ordering', 'asc');
    }

    /**
     * Get manufacturer name based on locale
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
     * Get manufacturer alias based on locale
     */
    public function getAliasAttribute(): string
    {
        $locale = app()->getLocale();
        
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
        
        // Fallback to Ukrainian alias
        return $this->attributes['alias_uk-UA'] ?? '';
    }

    /**
     * Get short description based on locale
     */
    public function getShortDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        
        $localeMap = [
            'uk' => 'uk-UA',
            'ru' => 'ru-UA', 
            'en' => 'en-GB'
        ];
        
        $dbLocale = $localeMap[$locale] ?? 'uk-UA';
        $descField = "short_description_{$dbLocale}";
        
        if (isset($this->attributes[$descField]) && !empty($this->attributes[$descField])) {
            return $this->attributes[$descField];
        }
        
        return $this->attributes['short_description_uk-UA'] ?? '';
    }

    /**
     * Get full description based on locale
     */
    public function getDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        
        $localeMap = [
            'uk' => 'uk-UA',
            'ru' => 'ru-UA', 
            'en' => 'en-GB'
        ];
        
        $dbLocale = $localeMap[$locale] ?? 'uk-UA';
        $descField = "description_{$dbLocale}";
        
        if (isset($this->attributes[$descField]) && !empty($this->attributes[$descField])) {
            return $this->attributes[$descField];
        }
        
        return $this->attributes['description_uk-UA'] ?? '';
    }

    /**
     * Get manufacturer logo URL
     */
    public function getLogoUrlAttribute(): string
    {
        if (!empty($this->manufacturer_logo)) {
            return asset('storage/manufacturers/' . $this->manufacturer_logo);
        }
        return asset('images/no-image.svg');
    }

    /**
     * Get manufacturer URL
     */
    public function getUrlAttribute(): string
    {
        if (!empty($this->manufacturer_url)) {
            return $this->manufacturer_url;
        }
        return route('manufacturers.show', $this->manufacturer_id);
    }

    /**
     * Relationship with products
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'product_manufacturer_id', 'manufacturer_id');
    }

    /**
     * Get products count
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->published()->count();
    }

    /**
     * Get base fields (non-localized) for Manufacturer model
     */
    protected static function getBaseFields(): array
    {
        return [
            'manufacturer_id',
            'code_1c',
            'manufacturer_url',
            'manufacturer_logo',
            'additional_image',
            'manufacturer_publish',
            'products_page',
            'products_row',
            'ordering',
            'manufacturer_status',
            'name_langmetadata',
            'alias_langmetadata',
            'short_description_langmetadata',
            'description_langmetadata'
        ];
    }
}
