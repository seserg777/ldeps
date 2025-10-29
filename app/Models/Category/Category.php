<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasLocalizedFields;
use App\Models\Product\Product;

class Category extends Model
{
    use HasFactory, HasLocalizedFields;

    protected $table = 'vjprf_jshopping_categories';
    protected $primaryKey = 'category_id';
    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'category_image',
        'category_parent_id',
        'category_publish',
        'category_ordertype',
        'category_template',
        'ordering',
        'category_add_date',
        'products_page',
        'products_row',
        'access',
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
        'meta_keyword_ru-UA'
    ];

    /**
     * Scope for published categories
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('category_publish', 1);
    }

    /**
     * Scope for root categories (parent_id = 0)
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->where('category_parent_id', 0);
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('ordering', 'asc');
    }

    /**
     * Get category name based on locale
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
     * Get category alias based on locale
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
        
        // Fallback to Ukrainian alias
        return $this->attributes['alias_uk-UA'] ?? 'category';
    }

    /**
     * Get category short description based on locale
     */
    public function getShortDescriptionAttribute(): string
    {
        $locale = app()->getLocale();
        
        // Map locale to database field format
        $localeMap = [
            'uk' => 'uk-UA',
            'ru' => 'ru-UA', 
            'en' => 'en-GB'
        ];
        
        $dbLocale = $localeMap[$locale] ?? 'uk-UA';
        $descriptionField = "short_description_{$dbLocale}";
        
        if (isset($this->attributes[$descriptionField]) && !empty($this->attributes[$descriptionField])) {
            return $this->attributes[$descriptionField];
        }
        
        // Fallback to Ukrainian description
        return $this->attributes['short_description_uk-UA'] ?? '';
    }

    /**
     * Get full hierarchical path for category
     */
    public function getFullPathAttribute(): string
    {
        $path = [];
        $current = $this;
        
        // Build path from current category to root
        while ($current) {
            $path[] = $current->alias;
            $current = $current->parent;
        }
        
        // Reverse to get root -> current order
        return implode('/', array_reverse($path));
    }

    /**
     * Get parent category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'category_parent_id', 'category_id');
    }

    /**
     * Relationship with products
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'vjprf_jshopping_products_to_categories', 'category_id', 'product_id');
    }

    /**
     * Relationship with subcategories
     */
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'category_parent_id', 'category_id');
    }

    /**
     * Get all descendants (subcategories and sub-subcategories)
     */
    public function descendants()
    {
        return $this->hasMany(Category::class, 'category_parent_id', 'category_id')
                    ->with('descendants');
    }

    /**
     * Get direct children only (first level subcategories)
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'category_parent_id', 'category_id');
    }

    /**
     * Relationship with complexes
     */
    public function complexes()
    {
        return $this->hasMany(Complex::class, 'category_id', 'category_id');
    }

    /**
     * Get base fields (non-localized) for Category model
     */
    protected static function getBaseFields(): array
    {
        return [
            'category_id',
            'category_image',
            'category_parent_id',
            'category_publish',
            'category_ordertype',
            'category_template',
            'ordering',
            'category_add_date',
            'products_page',
            'products_row',
            'access'
        ];
    }
}
