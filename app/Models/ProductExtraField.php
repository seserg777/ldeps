<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExtraField extends Model
{
    use HasFactory;

    protected $table = 'vjprf_jshopping_products_extra_fields';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'allcats',
        'cats',
        'type',
        'multilist',
        'group',
        'ordering',
        'name_en-GB',
        'description_en-GB',
        'name_uk-UA',
        'description_uk-UA',
        'name_ar-AA'
    ];

    /**
     * Get field name based on locale
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
     * Relationship with field values
     */
    public function values()
    {
        return $this->hasMany(ProductExtraFieldValue::class, 'field_id', 'id');
    }

    /**
     * Relationship with product characteristics
     */
    public function characteristics()
    {
        return $this->hasMany(ProductCharacteristic::class, 'extra_field', 'id');
    }
}
