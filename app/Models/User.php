<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vjprf_users';

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
        'username',
        'email',
        'password',
        'block',
        'sendEmail',
        'registerDate',
        'lastvisitDate',
        'activation',
        'params',
        'lastResetTime',
        'resetCount',
        'otpKey',
        'otep',
        'requireReset',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'otpKey',
        'otep',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'registerDate' => 'datetime',
        'lastvisitDate' => 'datetime',
        'lastResetTime' => 'datetime',
        'block' => 'boolean',
        'sendEmail' => 'boolean',
        'requireReset' => 'boolean',
        'resetCount' => 'integer',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return null; // vjprf_users table doesn't have remember_token
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // vjprf_users table doesn't have remember_token
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return null; // vjprf_users table doesn't have remember_token
    }

    /**
     * Get the user groups for the user.
     */
    public function groups()
    {
        return $this->belongsToMany(
            Usergroup::class,
            'vjprf_user_usergroup_map',
            'user_id',
            'group_id'
        );
    }

    /**
     * Get the user profiles for the user.
     */
    public function profiles()
    {
        return $this->hasMany(UserProfile::class, 'user_id');
    }

    /**
     * Get a specific profile value by key.
     *
     * @param string $key
     * @return string|null
     */
    public function getProfileValue($key)
    {
        $profile = $this->profiles()->where('profile_key', $key)->first();
        return $profile ? $profile->profile_value : null;
    }

    /**
     * Set a profile value by key.
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setProfileValue($key, $value)
    {
        $this->profiles()->updateOrCreate(
            ['profile_key' => $key],
            ['profile_value' => $value]
        );
    }

    /**
     * Check if user is blocked.
     *
     * @return bool
     */
    public function isBlocked()
    {
        return (bool) $this->block;
    }

    /**
     * Check if user requires password reset.
     *
     * @return bool
     */
    public function requiresPasswordReset()
    {
        return (bool) $this->requireReset;
    }
}