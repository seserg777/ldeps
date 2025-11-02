<?php

namespace Tests\Feature\Helpers;

use App\Helpers\MenuRenderer;
use App\Models\Menu\Menu;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuRendererTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_detects_active_menu_id_for_homepage()
    {
        $homeMenu = Menu::factory()->create([
            'home' => 1,
            'language' => 'uk-UA',
            'published' => 1,
        ]);

        app()->setLocale('uk');

        $activeMenuId = MenuRenderer::detectActiveMenuId();

        $this->assertEquals($homeMenu->id, $activeMenuId);
    }

    /** @test */
    public function it_gets_modules_for_page()
    {
        $menu = Menu::factory()->create(['published' => 1]);
        
        $module = Module::factory()->create([
            'published' => 1,
            'ordering' => 1,
        ]);

        // Attach module to menu
        $module->pages()->attach($menu->id);

        $modules = MenuRenderer::getModulesForPage($menu->id, false);

        $this->assertCount(1, $modules);
        $this->assertEquals($module->id, $modules->first()->id);
    }

    /** @test */
    public function it_includes_global_modules_when_requested()
    {
        $menu = Menu::factory()->create(['published' => 1]);
        
        // Module attached to menu
        $pageModule = Module::factory()->create([
            'published' => 1,
            'ordering' => 1,
            'title' => 'Page Module',
        ]);
        $pageModule->pages()->attach($menu->id);

        // Global module (not attached to any menu)
        $globalModule = Module::factory()->create([
            'published' => 1,
            'ordering' => 2,
            'title' => 'Global Module',
        ]);

        $modules = MenuRenderer::getModulesForPage($menu->id, true);

        $this->assertCount(2, $modules);
        $this->assertTrue($modules->contains('id', $pageModule->id));
        $this->assertTrue($modules->contains('id', $globalModule->id));
    }

    /** @test */
    public function it_filters_only_menu_modules()
    {
        $menuModule = Module::factory()->create([
            'module' => 'mod_menu',
            'published' => 1,
        ]);

        $otherModule = Module::factory()->create([
            'module' => 'mod_custom',
            'published' => 1,
        ]);

        $modules = collect([$menuModule, $otherModule]);

        $menuModules = MenuRenderer::getMenuModules($modules);

        $this->assertCount(1, $menuModules);
        $this->assertEquals($menuModule->id, $menuModules->first()->id);
    }

    /** @test */
    public function it_renders_menu_modules()
    {
        $module = Module::factory()->create([
            'module' => 'mod_menu',
            'published' => 1,
            'position' => 'header',
            'title' => 'Main Menu',
            'params' => json_encode(['menutype' => 'mainmenu']),
        ]);

        Menu::factory()->create([
            'menutype' => 'mainmenu',
            'level' => 1,
            'title' => 'Home',
            'published' => 1,
        ]);

        $renderedMenus = MenuRenderer::renderMenuModules(collect([$module]));

        $this->assertIsArray($renderedMenus);
        $this->assertArrayHasKey('header', $renderedMenus);
        $this->assertArrayHasKey('mainmenu', $renderedMenus);
        $this->assertStringContainsString('Home', $renderedMenus['mainmenu']);
    }

    /** @test */
    public function it_returns_empty_array_for_no_modules()
    {
        $renderedMenus = MenuRenderer::renderMenuModules(collect([]));

        $this->assertIsArray($renderedMenus);
        $this->assertEmpty($renderedMenus);
    }
}

