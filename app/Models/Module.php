<?php

namespace App\Models;

use App\Models\Menu\Menu;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vjprf_modules';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

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
        'asset_id',
        'title',
        'note',
        'content',
        'ordering',
        'position',
        'checked_out',
        'checked_out_time',
        'publish_up',
        'publish_down',
        'published',
        'module',
        'access',
        'showtitle',
        'params',
        'client_id',
        'language',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'published' => 'boolean',
        'showtitle' => 'boolean',
        'ordering' => 'integer',
        'access' => 'integer',
        'client_id' => 'integer',
        'checked_out_time' => 'datetime',
        'publish_up' => 'datetime',
        'publish_down' => 'datetime',
    ];

    /**
     * Get the menu items associated with this module.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function menuItems()
    {
        return $this->belongsToMany(
            Menu::class,
            'vjprf_modules_menu',
            'moduleid',
            'menuid',
            'id',
            'id'
        );
    }

    /**
     * Scope a query to only include published modules.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('published', 1);
    }

    /**
     * Scope a query to order by ordering field.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ordering', 'asc');
    }

    /**
     * Scope a query to filter by position.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $position
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope a query to filter by client_id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $clientId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClientId($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Get the module's params as array.
     *
     * @return array
     */
    public function getParamsArrayAttribute()
    {
        if (empty($this->params)) {
            return [];
        }

        $decoded = json_decode($this->params, true);
        return is_array($decoded) ? $decoded : [];
    }
}

