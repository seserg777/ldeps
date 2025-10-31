<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'vjprf_categories';

    protected $fillable = [
        'asset_id',
        'parent_id',
        'lft',
        'rgt',
        'level',
        'path',
        'extension',
        'title',
        'alias',
        'note',
        'description',
        'published',
        'checked_out',
        'checked_out_time',
        'access',
        'params',
        'metadesc',
        'metakey',
        'metadata',
        'created_user_id',
        'created_time',
        'modified_user_id',
        'modified_time',
        'hits',
        'language',
        'version'
    ];

    protected $casts = [
        'published' => 'boolean',
        'checked_out_time' => 'datetime',
        'created_time' => 'datetime',
        'modified_time' => 'datetime',
        'params' => 'array',
        'metadata' => 'array',
        'asset_id' => 'integer',
        'parent_id' => 'integer',
        'lft' => 'integer',
        'rgt' => 'integer',
        'level' => 'integer',
        'access' => 'integer',
        'created_user_id' => 'integer',
        'modified_user_id' => 'integer',
        'hits' => 'integer',
        'version' => 'integer'
    ];

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('lft');
    }

    /**
     * Get all descendants of the category.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get the content items in this category.
     */
    public function content()
    {
        return $this->hasMany(Content::class, 'catid');
    }

    /**
     * Scope a query to only include published categories.
     */
    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    /**
     * Scope a query to only include root level categories.
     */
    public function scopeRoot($query)
    {
        return $query->where('level', 1);
    }

    /**
     * Scope a query to only include categories of a specific extension.
     */
    public function scopeOfExtension($query, $extension)
    {
        return $query->where('extension', $extension);
    }

    /**
     * Get the full path for the category.
     */
    public function getFullPathAttribute()
    {
        return $this->path ?: $this->alias;
    }

    /**
     * Check if category is published.
     */
    public function isPublished()
    {
        return $this->published == 1;
    }

    /**
     * Check if category is checked out.
     */
    public function isCheckedOut()
    {
        return !is_null($this->checked_out);
    }

    /**
     * Get the count of published content in this category.
     */
    public function getPublishedContentCountAttribute()
    {
        return $this->content()->where('state', 1)->count();
    }

    /**
     * Get the count of unpublished content in this category.
     */
    public function getUnpublishedContentCountAttribute()
    {
        return $this->content()->where('state', 0)->count();
    }

    /**
     * Get the count of archived content in this category.
     */
    public function getArchivedContentCountAttribute()
    {
        return $this->content()->where('state', 2)->count();
    }

    /**
     * Get the count of trashed content in this category.
     */
    public function getTrashedContentCountAttribute()
    {
        return $this->content()->where('state', -2)->count();
    }
}
