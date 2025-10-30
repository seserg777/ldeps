<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exussalebanner extends Model
{
    protected $table = 'vjprf_exussalebanner';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'alias',
        'description',
        'image',
        'link',
        'published',
        'ordering',
        'created',
        'modified'
    ];

    protected $casts = [
        'published' => 'boolean',
        'created' => 'datetime',
        'modified' => 'datetime'
    ];

    /**
     * Scope for published banners
     */
    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    /**
     * Scope for ordered banners
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordering', 'asc');
    }
}