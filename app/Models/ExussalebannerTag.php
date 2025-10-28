<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExussalebannerTag extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vjprf_exussalebanner_tags';

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
        'name',
        'alias',
        'image',
        'created',
        'ordering',
        'metakey',
        'metadesc',
        'hits',
        'created_by',
        'published',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created' => 'datetime',
        'published' => 'boolean',
        'ordering' => 'integer',
        'hits' => 'integer',
        'created_by' => 'integer',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the sale banners that have this tag.
     */
    public function saleBanners()
    {
        return $this->belongsToMany(
            Exussalebanner::class,
            'vjprf_exussalebanner_sale_tags',
            'tag_id',
            'sale_id'
        );
    }

    /**
     * Scope to get only published tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    /**
     * Scope to get tags ordered by ordering field.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordering');
    }

    /**
     * Check if the tag is published.
     *
     * @return bool
     */
    public function isPublished()
    {
        return (bool) $this->published;
    }

    /**
     * Get the tag URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return route('sale-banner.tag', $this->alias);
    }
}
