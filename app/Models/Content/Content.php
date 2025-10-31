<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $table = 'vjprf_content';

    protected $fillable = [
        'asset_id',
        'title',
        'alias',
        'introtext',
        'fulltext',
        'state',
        'catid',
        'created',
        'created_by',
        'created_by_alias',
        'modified',
        'modified_by',
        'checked_out',
        'checked_out_time',
        'publish_up',
        'publish_down',
        'images',
        'urls',
        'attribs',
        'version',
        'ordering',
        'metakey',
        'metadesc',
        'access',
        'hits',
        'metadata',
        'featured',
        'language',
        'note'
    ];

    protected $casts = [
        'created' => 'datetime',
        'modified' => 'datetime',
        'checked_out_time' => 'datetime',
        'publish_up' => 'datetime',
        'publish_down' => 'datetime',
        'images' => 'array',
        'urls' => 'array',
        'attribs' => 'array',
        'metadata' => 'array',
        'featured' => 'boolean',
        'asset_id' => 'integer',
        'catid' => 'integer',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'checked_out' => 'integer',
        'version' => 'integer',
        'ordering' => 'integer',
        'access' => 'integer',
        'hits' => 'integer'
    ];

    /**
     * Get the category that owns the content.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'catid');
    }

    /**
     * Scope a query to only include published content.
     */
    public function scopePublished($query)
    {
        return $query->where('state', 1);
    }

    /**
     * Scope a query to only include unpublished content.
     */
    public function scopeUnpublished($query)
    {
        return $query->where('state', 0);
    }

    /**
     * Scope a query to only include archived content.
     */
    public function scopeArchived($query)
    {
        return $query->where('state', 2);
    }

    /**
     * Scope a query to only include trashed content.
     */
    public function scopeTrashed($query)
    {
        return $query->where('state', -2);
    }

    /**
     * Scope a query to only include featured content.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', 1);
    }

    /**
     * Scope a query to only include content of a specific category.
     */
    public function scopeOfCategory($query, $catid)
    {
        return $query->where('catid', $catid);
    }

    /**
     * Scope a query to only include content of a specific language.
     */
    public function scopeOfLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Check if content is published.
     */
    public function isPublished()
    {
        return $this->state == 1;
    }

    /**
     * Check if content is unpublished.
     */
    public function isUnpublished()
    {
        return $this->state == 0;
    }

    /**
     * Check if content is archived.
     */
    public function isArchived()
    {
        return $this->state == 2;
    }

    /**
     * Check if content is trashed.
     */
    public function isTrashed()
    {
        return $this->state == -2;
    }

    /**
     * Check if content is featured.
     */
    public function isFeatured()
    {
        return $this->featured == 1;
    }

    /**
     * Check if content is checked out.
     */
    public function isCheckedOut()
    {
        return !is_null($this->checked_out);
    }

    /**
     * Get the full text content (introtext + fulltext).
     */
    public function getFullContentAttribute()
    {
        return $this->introtext . $this->fulltext;
    }

    /**
     * Get the excerpt of the content.
     */
    public function getExcerptAttribute()
    {
        return \Str::limit(strip_tags($this->introtext ?: $this->fulltext), 200);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        switch ($this->state) {
            case 1:
                return 'Опубликовано';
            case 0:
                return 'Не опубликовано';
            case 2:
                return 'В архиве';
            case -2:
                return 'В корзине';
            default:
                return 'Неизвестно';
        }
    }
}
