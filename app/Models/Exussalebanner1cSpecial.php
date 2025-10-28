<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exussalebanner1cSpecial extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vjprf_exussalebanner_1c_special';

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
        'sale_id',
        'published',
        'key_1c',
        'qty',
        'name',
        'key_1c_exp',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published' => 'boolean',
        'qty' => 'decimal:4',
        'sale_id' => 'integer',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the sale banner that owns this special offer.
     */
    public function saleBanner()
    {
        return $this->belongsTo(Exussalebanner::class, 'sale_id');
    }

    /**
     * Scope to get only published special offers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    /**
     * Check if the special offer is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return (bool) $this->published;
    }
}
