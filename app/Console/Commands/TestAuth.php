<?php

namespace App\Console\Commands;

use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:test {username} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user authentication with MD5 passwords';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->argument('password');

        $this->info("Testing authentication for user: $username");

        // Find user by username only
        $user = User::where('username', $username)->first();

        if (!$user) {
            $this->error("User not found: $username");
            return 1;
        }

        $this->info("User found: ID={$user->id}, Name={$user->name}, Email={$user->email}");
        $this->info("User blocked: " . ($user->isBlocked() ? 'YES' : 'NO'));
        $this->info("Password hash: {$user->password}");

        // Test password verification
        $isValid = Hash::driver('md5')->check($password, $user->password);

        if ($isValid) {
            $this->info("✅ Password verification: SUCCESS");
        } else {
            $this->error("❌ Password verification: FAILED");
        }

        // Show user groups
        $groups = $user->groups;
        if ($groups->count() > 0) {
            $this->info("User groups:");
            foreach ($groups as $group) {
                $this->line("  - {$group->title} (ID: {$group->id})");
            }
        } else {
            $this->info("No groups assigned to user");
        }

        // Show profile data
        $profiles = $user->profiles;
        if ($profiles->count() > 0) {
            $this->info("Profile data:");
            foreach ($profiles as $profile) {
                $this->line("  - {$profile->profile_key}: {$profile->profile_value}");
            }
        } else {
            $this->info("No profile data found");
        }

        return $isValid ? 0 : 1;
    }
}
