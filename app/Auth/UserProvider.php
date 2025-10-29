<?php

namespace App\Auth;

use App\Models\User\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Hash;

class UserProvider extends EloquentUserProvider
{
    /**
     * Create a new database user provider.
     *
     * @param \Illuminate\Contracts\Hashing\Hasher $hasher
     * @param string $model
     */
    public function __construct(Hasher $hasher, $model)
    {
        parent::__construct($hasher, $model);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];
        $hashedPassword = $user->getAuthPassword();

        // Check if password is MD5 (32 characters) or bcrypt (starts with $2y$)
        if (strlen($hashedPassword) === 32) {
            // MD5 password
            return Hash::driver('md5')->check($plain, $hashedPassword);
        } elseif (str_starts_with($hashedPassword, '$2y$')) {
            // Bcrypt password
            return Hash::check($plain, $hashedPassword);
        }

        return false;
    }
}
