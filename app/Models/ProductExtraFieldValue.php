<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductExtraFieldValue extends Model
{
    use HasFactory;

    protected $table = 'vjprf_jshopping_products_extra_field_values';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'field_id',
        'ordering',
        'name_en-GB',
        'name_uk-UA',
        'name_ar-AA',
        'name_es-ES',
        'name_langmetadata',
        'name_ru-UA',
        'name_ru-RU'
    ];

    /**
     * Get value name based on locale
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
     * Relationship with extra field
     */
    public function extraField()
    {
        return $this->belongsTo(ProductExtraField::class, 'field_id', 'id');
    }

    /**
     * Relationship with product characteristics
     */
    public function characteristics()
    {
        return $this->hasMany(ProductCharacteristic::class, 'extra_field_value', 'id');
    }
}
