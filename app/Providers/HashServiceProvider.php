<?php

namespace App\Providers;

use App\Hashing\Md5Hasher;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;

class HashServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register MD5 hasher
        Hash::extend('md5', function () {
            return new Md5Hasher();
        });
    }
}
