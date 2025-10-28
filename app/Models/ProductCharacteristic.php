<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCharacteristic extends Model
{
    use HasFactory;

    protected $table = 'vjprf_jshopping_products_characteristic';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'extra_field',
        'extra_field_value',
        'product_id'
    ];

    /**
     * Relationship with product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Relationship with extra field
     */
    public function extraField()
    {
        return $this->belongsTo(ProductExtraField::class, 'extra_field', 'id');
    }

    /**
     * Relationship with extra field value
     */
    public function extraFieldValue()
    {
        return $this->belongsTo(ProductExtraFieldValue::class, 'extra_field_value', 'id');
    }
}
