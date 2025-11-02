<?php

namespace Tests\Feature\Auth;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_login_form()
    {
        $response = $this->get(route('auth.login'));

        $response->assertStatus(200);
        $response->assertViewIs('front.auth.login');
    }

    /** @test */
    public function it_authenticates_user_with_valid_credentials()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::driver('md5')->make('password123'),
            'block' => 0,
        ]);

        $response = $this->post(route('auth.login'), [
            'username' => 'testuser',
            'password' => 'password123',
            'redirect' => '/',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user, 'custom');
    }

    /** @test */
    public function it_fails_authentication_with_invalid_password()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::driver('md5')->make('password123'),
            'block' => 0,
        ]);

        $response = $this->post(route('auth.login'), [
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest('custom');
    }

    /** @test */
    public function it_fails_authentication_for_blocked_user()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::driver('md5')->make('password123'),
            'block' => 1, // Blocked
        ]);

        $response = $this->post(route('auth.login'), [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest('custom');
    }

    /** @test */
    public function it_fails_authentication_with_nonexistent_user()
    {
        $response = $this->post(route('auth.login'), [
            'username' => 'nonexistent',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest('custom');
    }

    /** @test */
    public function it_logs_out_authenticated_user()
    {
        $user = User::factory()->create(['block' => 0]);

        $this->actingAs($user, 'custom');

        $response = $this->post(route('auth.logout'));

        $response->assertRedirect();
        $this->assertGuest('custom');
    }

    /** @test */
    public function it_redirects_to_specified_url_after_login()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::driver('md5')->make('password123'),
            'block' => 0,
        ]);

        $response = $this->post(route('auth.login'), [
            'username' => 'testuser',
            'password' => 'password123',
            'redirect' => '/about.html',
        ]);

        $response->assertRedirect('/about.html');
    }

    /** @test */
    public function it_updates_last_visit_date_on_login()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::driver('md5')->make('password123'),
            'block' => 0,
            'lastvisitDate' => null,
        ]);

        $this->post(route('auth.login'), [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $user->refresh();
        $this->assertNotNull($user->lastvisitDate);
    }
}

