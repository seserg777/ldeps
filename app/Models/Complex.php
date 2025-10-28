<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasLocalizedFields;

class Complex extends Model
{
    use HasFactory, HasLocalizedFields;

    protected $table = 'vjprf_jshopping_complex';
    protected $primaryKey = 'complex_id';
    public $timestamps = false;

    protected $fillable = [
        'complex_id',
        'complex_filter_link',
        'category_id',
        'complex_publish',
        'ordering',
        'name_en-GB',
        'description_en-GB',
        'description2_en-GB',
        'meta_title_en-GB',
        'meta_description_en-GB',
        'meta_keyword_en-GB',
        'name_uk-UA',
        'description_uk-UA',
        'description2_uk-UA',
        'meta_title_uk-UA',
        'meta_description_uk-UA',
        'meta_keyword_uk-UA',
        'name_ru-UA',
        'description_ru-UA',
        'meta_title_ru-UA',
        'meta_description_ru-UA',
        'meta_keyword_ru-UA'
    ];

    /**
     * Scope for published complexes
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('complex_publish', 1);
    }

    /**
     * Scope for ordered complexes
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('ordering', 'asc');
    }

    /**
     * Get complex name based on locale
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
     * Get complex alias based on locale (generated from name since alias field doesn't exist)
     */
    public function getAliasAttribute(): string
    {
        // Generate alias from name since alias field doesn't exist in database
        $name = $this->name;
        return \Str::slug($name);
    }

    /**
     * Get full path for complex
     */
    public function getFullPathAttribute(): string
    {
        $category = Category::find($this->category_id);
        if ($category) {
            return $category->full_path . '/' . $this->alias;
        }
        return $this->alias;
    }

    /**
     * Get URL for complex with query parameter
     */
    public function getComplexUrlAttribute(): string
    {
        $category = Category::find($this->category_id);
        if ($category) {
            $baseUrl = route('category.show', $category->full_path);
            $filterLink = $this->complex_filter_link ?: $this->complex_id;
            return $baseUrl . '?f=' . $filterLink;
        }
        return '#' . $this->complex_id;
    }

    /**
     * Relationship with category
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    /**
     * Get base fields (non-localized) for Complex model
     */
    protected static function getBaseFields(): array
    {
        return [
            'complex_id',
            'complex_filter_link',
            'category_id',
            'complex_publish',
            'ordering'
        ];
    }
}
