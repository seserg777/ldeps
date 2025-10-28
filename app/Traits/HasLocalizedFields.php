<?php

namespace App\Traits;

trait HasLocalizedFields
{
    /**
     * Language mapping for database fields
     */
    protected static $localeMap = [
        'uk' => 'uk-UA',
        'ru' => 'ru-UA', 
        'en' => 'en-GB'
    ];

    /**
     * Field types that have language variants (default set)
     */
    protected static $defaultLocalizedFieldTypes = [
        'name',
        'alias', 
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keyword'
    ];

    /**
     * Field types that have language variants for specific models
     */
    protected static $modelSpecificLocalizedFieldTypes = [
        'Product' => [
            'name',
            'alias', 
            'short_description',
            'description',
            'meta_title',
            'meta_description',
            'meta_keyword',
            'characteristics',
            'delivery_kit',
            'video_review',
            'documentation',
            'articles',
            'full_description',
            'calc'
        ],
        'Category' => [
            'name',
            'alias', 
            'short_description',
            'description',
            'meta_title',
            'meta_description',
            'meta_keyword'
        ],
        'Manufacturer' => [
            'name',
            'alias', 
            'short_description',
            'description',
            'meta_title',
            'meta_description',
            'meta_keyword'
        ],
        'Complex' => [
            'name',
            'description',
            'meta_title',
            'meta_description',
            'meta_keyword'
        ]
    ];

    /**
     * Get localized field types for current model
     */
    protected static function getLocalizedFieldTypes(): array
    {
        $modelName = class_basename(static::class);
        
        if (isset(static::$modelSpecificLocalizedFieldTypes[$modelName])) {
            return static::$modelSpecificLocalizedFieldTypes[$modelName];
        }
        
        return static::$defaultLocalizedFieldTypes;
    }

    /**
     * Get localized field name based on current locale
     */
    public static function getLocalizedFieldName(string $fieldType): string
    {
        $locale = app()->getLocale();
        $dbLocale = static::$localeMap[$locale] ?? 'uk-UA';
        return "{$fieldType}_{$dbLocale}";
    }

    /**
     * Get all localized field names for current locale
     */
    public static function getLocalizedFields(): array
    {
        $locale = app()->getLocale();
        $dbLocale = static::$localeMap[$locale] ?? 'uk-UA';
        
        $fields = [];
        foreach (static::getLocalizedFieldTypes() as $fieldType) {
            $fields[] = "{$fieldType}_{$dbLocale}";
        }
        
        return $fields;
    }

    /**
     * Get select fields with only current language columns
     */
    public static function getOptimizedSelectFields(): array
    {
        $baseFields = static::getBaseFields();
        $localizedFields = static::getLocalizedFields();
        
        return array_merge($baseFields, $localizedFields);
    }

    /**
     * Get base fields (non-localized)
     */
    protected static function getBaseFields(): array
    {
        // This should be overridden in each model
        return ['*'];
    }

    /**
     * Scope to select only necessary fields for current locale
     */
    public function scopeWithLocaleFields($query)
    {
        $selectFields = static::getOptimizedSelectFields();
        return $query->select($selectFields);
    }

    /**
     * Get localized field value with fallback
     */
    public function getLocalizedField(string $fieldType, ?string $fallback = null): string
    {
        $fieldName = static::getLocalizedFieldName($fieldType);
        
        if (isset($this->attributes[$fieldName]) && !empty($this->attributes[$fieldName])) {
            return $this->attributes[$fieldName];
        }
        
        // Fallback to Ukrainian if current locale field is empty
        if ($fieldType !== 'uk-UA') {
            $fallbackFieldName = "{$fieldType}_uk-UA";
            if (isset($this->attributes[$fallbackFieldName]) && !empty($this->attributes[$fallbackFieldName])) {
                return $this->attributes[$fallbackFieldName];
            }
        }
        
        return $fallback ?? '';
    }

    /**
     * Get localized field value with specific locale
     */
    public function getLocalizedFieldForLocale(string $fieldType, string $locale): string
    {
        $dbLocale = static::$localeMap[$locale] ?? 'uk-UA';
        $fieldName = "{$fieldType}_{$dbLocale}";
        
        if (isset($this->attributes[$fieldName]) && !empty($this->attributes[$fieldName])) {
            return $this->attributes[$fieldName];
        }
        
        // Fallback to Ukrainian
        $fallbackFieldName = "{$fieldType}_uk-UA";
        if (isset($this->attributes[$fallbackFieldName]) && !empty($this->attributes[$fallbackFieldName])) {
            return $this->attributes[$fallbackFieldName];
        }
        
        return '';
    }
}
