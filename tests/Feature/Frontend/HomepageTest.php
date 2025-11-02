<?php

namespace Tests\Feature\Frontend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_homepage_successfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('front.homepage');
    }

    /** @test */
    public function it_includes_rendered_menus_in_homepage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('renderedMenus');
    }

    /** @test */
    public function it_includes_active_menu_id_in_homepage()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('activeMenuId');
    }

    /** @test */
    public function it_displays_user_modal_login_component()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('user-menu', false); // Check for class
    }

    /** @test */
    public function it_loads_alpine_js_assets()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Check that Vite assets are referenced
        $this->assertTrue(
            str_contains($response->getContent(), 'resources/js/app.js') ||
            str_contains($response->getContent(), 'build/assets/app')
        );
    }
}

