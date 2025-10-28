<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExussalebannerJshop extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vjprf_exussalebanner_jshop';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'cat_id',
        'sale_id',
        'oldprice',
        'price',
        'name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_id' => 'integer',
        'cat_id' => 'integer',
        'sale_id' => 'integer',
        'oldprice' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the sale banner that owns this JShop product.
     */
    public function saleBanner()
    {
        return $this->belongsTo(Exussalebanner::class, 'sale_id');
    }

    /**
     * Get the discount percentage.
     *
     * @return float
     */
    public function getDiscountPercentage()
    {
        if ($this->oldprice <= 0) {
            return 0;
        }

        return round((($this->oldprice - $this->price) / $this->oldprice) * 100, 2);
    }

    /**
     * Get the discount amount.
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->oldprice - $this->price;
    }

    /**
     * Check if the product has a discount.
     *
     * @return bool
     */
    public function hasDiscount()
    {
        return $this->oldprice > $this->price;
    }
}
