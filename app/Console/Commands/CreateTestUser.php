<?php

namespace App\Console\Commands;

use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:create-test-user {username} {email} {password} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test user with MD5 password';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $username = $this->argument('username');
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->option('name') ?: $username;

        $this->info("Creating test user...");

        // Check if user already exists
        $existingUser = User::where('username', $username)
                                ->orWhere('email', $email)
                                ->first();

        if ($existingUser) {
            $this->error("User already exists with username '$username' or email '$email'");
            return 1;
        }

        // Create user with MD5 password
        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::driver('md5')->make($password),
            'block' => 0,
            'sendEmail' => 0,
            'registerDate' => now(),
            'lastvisitDate' => null,
            'activation' => '',
            'params' => '',
            'lastResetTime' => null,
            'resetCount' => 0,
            'otpKey' => '',
            'otep' => '',
            'requireReset' => 0,
        ]);

        $this->info("✅ User created successfully!");
        $this->info("ID: {$user->id}");
        $this->info("Name: {$user->name}");
        $this->info("Username: {$user->username}");
        $this->info("Email: {$user->email}");
        $this->info("Password hash: {$user->password}");

        // Test the created user
        $this->info("\nTesting authentication...");
        $isValid = Hash::driver('md5')->check($password, $user->password);
        
        if ($isValid) {
            $this->info("✅ Password verification: SUCCESS");
        } else {
            $this->error("❌ Password verification: FAILED");
        }

        return 0;
    }
}
