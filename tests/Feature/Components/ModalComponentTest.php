<?php

namespace Tests\Feature\Components;

use Tests\TestCase;

class ModalComponentTest extends TestCase
{
    /** @test */
    public function it_renders_base_modal_component()
    {
        $view = $this->blade(
            '<x-modal name="test-modal" title="Test Title">Content</x-modal>'
        );

        $view->assertSee('test-modal', false);
        $view->assertSee('Test Title');
        $view->assertSee('Content');
        $view->assertSee('x-data', false);
        $view->assertSee('x-show', false);
    }

    /** @test */
    public function it_renders_modal_with_correct_max_width()
    {
        $view = $this->blade(
            '<x-modal name="test-modal" max-width="md">Content</x-modal>'
        );

        $view->assertSee('sm:max-w-md', false);
    }

    /** @test */
    public function it_renders_modal_without_title()
    {
        $view = $this->blade(
            '<x-modal name="test-modal">Content</x-modal>'
        );

        $view->assertSee('Content');
        $view->assertDontSee('border-b', false);
    }

    /** @test */
    public function it_renders_auth_modal_component()
    {
        $view = $this->blade('<x-auth-modal />');

        $view->assertSee('Увійти в акаунт');
        $view->assertSee('Електронна пошта або логін');
        $view->assertSee('Пароль');
        $view->assertSee('Запам\'ятати мене');
        $view->assertSee('Забули свій пароль?');
        $view->assertSee('Зареєструватися');
    }

    /** @test */
    public function it_includes_alpine_directives_in_modal()
    {
        $view = $this->blade(
            '<x-modal name="test-modal">Content</x-modal>'
        );

        $view->assertSee('x-data', false);
        $view->assertSee('x-show', false);
        $view->assertSee('x-on:open-modal.window', false);
        $view->assertSee('x-on:close-modal.window', false);
        $view->assertSee('x-on:keydown.escape.window', false);
    }

    /** @test */
    public function it_renders_user_modal_login_for_guest()
    {
        $view = $this->blade('<x-user-modal-login />');

        $view->assertSee('Увійти', false); // aria-label
        $view->assertSee('x-data', false);
        $view->assertSee('open-modal', false);
        $view->assertSee('auth-modal', false);
    }

    /** @test */
    public function it_renders_user_modal_login_for_authenticated_user()
    {
        $user = \App\Models\User\User::factory()->create();

        $this->actingAs($user, 'custom');

        $view = $this->blade('<x-user-modal-login />');

        $view->assertSee('Профіль користувача', false); // aria-label
        $view->assertSee('profile-modal', false);
    }
}

