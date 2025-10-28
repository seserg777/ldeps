<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLocalizedFields;

class Exussalebanner extends Model
{
    use HasLocalizedFields;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vjprf_exussalebanner';

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
        'is_no_design',
        'is_view_products',
        'is_1c_promo',
        'title',
        'alias',
        'asset_id',
        'image',
        'list_text',
        'introtext',
        'fulltext',
        'created',
        'sale_start',
        'sale_end',
        'ordering',
        'metakey',
        'metadesc',
        'hits',
        'created_by',
        'published',
        'title_ru-UA',
        'alias_ru-UA',
        'image_ru-UA',
        'introtext_ru-UA',
        'fulltext_ru-UA',
        'metakey_ru-UA',
        'metadesc_ru-UA',
        'title_uk-UA',
        'alias_uk-UA',
        'image_uk-UA',
        'introtext_uk-UA',
        'fulltext_uk-UA',
        'metakey_uk-UA',
        'metadesc_uk-UA',
        'title_en-GB',
        'alias_en-GB',
        'image_en-GB',
        'introtext_en-GB',
        'fulltext_en-GB',
        'metakey_en-GB',
        'metadesc_en-GB',
        'conditions_ru-UA',
        'conditions_en-GB',
        'conditions_uk-UA',
        'metatitle_ru-UA',
        'metatitle_uk-UA',
        'metatitle_en-GB',
        'intro_text_ru-UA',
        'intro_text_uk-UA',
        'intro_text_en-GB',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created' => 'datetime',
        'sale_start' => 'datetime',
        'sale_end' => 'datetime',
        'is_no_design' => 'boolean',
        'is_view_products' => 'boolean',
        'is_1c_promo' => 'boolean',
        'published' => 'boolean',
        'ordering' => 'integer',
        'hits' => 'integer',
        'created_by' => 'integer',
        'asset_id' => 'integer',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the localized fields configuration.
     *
     * @return array
     */
    public function getLocalizedFields(): array
    {
        return [
            'title' => ['ru-UA', 'uk-UA', 'en-GB'],
            'alias' => ['ru-UA', 'uk-UA', 'en-GB'],
            'image' => ['ru-UA', 'uk-UA', 'en-GB'],
            'introtext' => ['ru-UA', 'uk-UA', 'en-GB'],
            'fulltext' => ['ru-UA', 'uk-UA', 'en-GB'],
            'metakey' => ['ru-UA', 'uk-UA', 'en-GB'],
            'metadesc' => ['ru-UA', 'uk-UA', 'en-GB'],
            'conditions' => ['ru-UA', 'uk-UA', 'en-GB'],
            'metatitle' => ['ru-UA', 'uk-UA', 'en-GB'],
            'intro_text' => ['ru-UA', 'uk-UA', 'en-GB'],
        ];
    }

    /**
     * Get the 1C special offers for this sale banner.
     */
    public function specialOffers()
    {
        return $this->hasMany(Exussalebanner1cSpecial::class, 'sale_id');
    }

    /**
     * Get the JShop products for this sale banner.
     */
    public function jshopProducts()
    {
        return $this->hasMany(ExussalebannerJshop::class, 'sale_id');
    }

    /**
     * Get the tags for this sale banner.
     */
    public function tags()
    {
        return $this->belongsToMany(
            ExussalebannerTag::class,
            'vjprf_exussalebanner_sale_tags',
            'sale_id',
            'tag_id'
        );
    }

    /**
     * Scope to get only published sale banners.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    /**
     * Scope to get active sale banners (within date range).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        $now = now();
        return $query->where('sale_start', '<=', $now)
                    ->where('sale_end', '>=', $now);
    }

    /**
     * Scope to get sale banners ordered by ordering field.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordering');
    }

    /**
     * Check if the sale banner is currently active.
     *
     * @return bool
     */
    public function isActive()
    {
        $now = now();
        return $this->sale_start <= $now && $this->sale_end >= $now;
    }

    /**
     * Check if the sale banner is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return (bool) $this->published;
    }

    /**
     * Get the sale banner URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return route('sale-banner.show', $this->alias);
    }

    /**
     * Increment hits counter.
     *
     * @return void
     */
    public function incrementHits()
    {
        $this->increment('hits');
    }
}
