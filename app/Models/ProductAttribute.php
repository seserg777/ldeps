<?php

namespace App\Models;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vjprf_jshopping_products_attr';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'product_attr_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'retail_price',
        'buy_price',
        'price',
        'special_price',
        'unit',
        'count',
        'ean',
        'manufacturer_code',
        'weight',
        'weight_volume_units',
        'old_price',
        'ext_attribute_product_id',
        'attr_12',
        'attr_13',
        'attr_14',
        'attr_15',
        'attr_16',
        'attr_17',
        'attr_18',
        'attr_19',
        'attr_20',
        'attr_22',
        'attr_23',
        'attr_24',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'retail_price' => 'decimal:2',
        'buy_price' => 'decimal:2',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'weight' => 'decimal:4',
        'weight_volume_units' => 'decimal:4',
        'special_price' => 'boolean',
        'count' => 'integer',
        'unit' => 'integer',
    ];

    /**
     * Get the product that owns the attribute.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get attribute values for display.
     *
     * @return array
     */
    public function getAttributeValues(): array
    {
        $values = [];
        
        for ($i = 12; $i <= 24; $i++) {
            if ($i == 21) continue; // Skip attr_21 as it doesn't exist in table
            
            $attrKey = "attr_{$i}";
            if (!is_null($this->$attrKey)) {
                $values[$i] = $this->$attrKey;
            }
        }
        
        return $values;
    }
}

