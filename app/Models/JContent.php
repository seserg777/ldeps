<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JContent extends Model
{
    protected $table = 'vjprf_content';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'title', 'alias', 'introtext', 'fulltext', 'state', 'created'
    ];

    public function scopePublished($query)
    {
        return $query->where('state', 1);
    }
}
