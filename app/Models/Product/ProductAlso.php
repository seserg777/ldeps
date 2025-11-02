<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAlso extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vjprf_jshopping_products_alsos';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'product_also_id',
    ];

    /**
     * Get the main product.
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    /**
     * Get the related product (also bought).
     *
     * @return BelongsTo
     */
    public function alsoProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_also_id', 'product_id');
    }
}

