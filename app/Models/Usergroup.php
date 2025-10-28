<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usergroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vjprf_usergroups';

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
        'parent_id',
        'lft',
        'rgt',
        'title',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parent_id' => 'integer',
        'lft' => 'integer',
        'rgt' => 'integer',
    ];

    /**
     * Get the parent group.
     */
    public function parent()
    {
        return $this->belongsTo(Usergroup::class, 'parent_id');
    }

    /**
     * Get the child groups.
     */
    public function children()
    {
        return $this->hasMany(Usergroup::class, 'parent_id');
    }

    /**
     * Get all users in this group.
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'vjprf_user_usergroup_map',
            'group_id',
            'user_id'
        );
    }

    /**
     * Check if this group is a child of another group.
     *
     * @param Usergroup $group
     * @return bool
     */
    public function isChildOf(Usergroup $group)
    {
        return $this->lft > $group->lft && $this->rgt < $group->rgt;
    }

    /**
     * Check if this group is a parent of another group.
     *
     * @param Usergroup $group
     * @return bool
     */
    public function isParentOf(Usergroup $group)
    {
        return $this->lft < $group->lft && $this->rgt > $group->rgt;
    }

    /**
     * Get all descendant groups.
     */
    public function descendants()
    {
        return $this->where('lft', '>', $this->lft)
                    ->where('rgt', '<', $this->rgt)
                    ->orderBy('lft');
    }

    /**
     * Get all ancestor groups.
     */
    public function ancestors()
    {
        return $this->where('lft', '<', $this->lft)
                    ->where('rgt', '>', $this->rgt)
                    ->orderBy('lft');
    }
}
