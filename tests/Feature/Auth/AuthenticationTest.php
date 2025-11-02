<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function it_requires_username_for_login()
    {
        $response = $this->post(route('auth.login'), [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
    }

    /** @test */
    public function it_requires_password_for_login()
    {
        $response = $this->post(route('auth.login'), [
            'username' => 'testuser',
        ]);

        $response->assertSessionHasErrors('password');
    }
}

