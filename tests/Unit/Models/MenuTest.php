<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Menu\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_home_menu_id_for_specific_language()
    {
        // Create home menu items for different languages
        $ukHomeMenu = Menu::create([
            'menutype' => 'mainmenu',
            'title' => 'Home UK',
            'alias' => 'home-uk',
            'link' => 'index.php?option=com_content&view=featured',
            'type' => 'component',
            'published' => 1,
            'home' => 1,
            'language' => 'uk-UA',
            'parent_id' => 0,
            'level' => 1,
            'lft' => 1,
            'rgt' => 2,
        ]);

        $ruHomeMenu = Menu::create([
            'menutype' => 'mainmenu',
            'title' => 'Home RU',
            'alias' => 'home-ru',
            'link' => 'index.php?option=com_content&view=featured',
            'type' => 'component',
            'published' => 1,
            'home' => 1,
            'language' => 'ru-UA',
            'parent_id' => 0,
            'level' => 1,
            'lft' => 3,
            'rgt' => 4,
        ]);

        // Test getting home menu ID for UK
        $homeIdUk = Menu::getHomeMenuId('uk-UA');
        $this->assertEquals($ukHomeMenu->id, $homeIdUk);

        // Test getting home menu ID for RU
        $homeIdRu = Menu::getHomeMenuId('ru-UA');
        $this->assertEquals($ruHomeMenu->id, $homeIdRu);
    }

    /** @test */
    public function it_returns_null_when_no_home_menu_exists_for_language()
    {
        $homeId = Menu::getHomeMenuId('en-GB');
        $this->assertNull($homeId);
    }

    /** @test */
    public function it_uses_current_locale_when_no_language_specified()
    {
        app()->setLocale('uk-UA');

        $ukHomeMenu = Menu::create([
            'menutype' => 'mainmenu',
            'title' => 'Home UK',
            'alias' => 'home-uk',
            'link' => 'index.php?option=com_content&view=featured',
            'type' => 'component',
            'published' => 1,
            'home' => 1,
            'language' => 'uk-UA',
            'parent_id' => 0,
            'level' => 1,
            'lft' => 1,
            'rgt' => 2,
        ]);

        $homeId = Menu::getHomeMenuId();
        $this->assertEquals($ukHomeMenu->id, $homeId);
    }

    /** @test */
    public function it_only_returns_published_home_menu()
    {
        Menu::create([
            'menutype' => 'mainmenu',
            'title' => 'Unpublished Home',
            'alias' => 'home-unpublished',
            'link' => 'index.php?option=com_content&view=featured',
            'type' => 'component',
            'published' => 0,
            'home' => 1,
            'language' => 'uk-UA',
            'parent_id' => 0,
            'level' => 1,
            'lft' => 1,
            'rgt' => 2,
        ]);

        $homeId = Menu::getHomeMenuId('uk-UA');
        $this->assertNull($homeId);
    }

    /** @test */
    public function home_scope_filters_by_language()
    {
        Menu::create([
            'menutype' => 'mainmenu',
            'title' => 'Home UK',
            'alias' => 'home-uk',
            'link' => 'index.php?option=com_content&view=featured',
            'type' => 'component',
            'published' => 1,
            'home' => 1,
            'language' => 'uk-UA',
            'parent_id' => 0,
            'level' => 1,
            'lft' => 1,
            'rgt' => 2,
        ]);

        Menu::create([
            'menutype' => 'mainmenu',
            'title' => 'Home RU',
            'alias' => 'home-ru',
            'link' => 'index.php?option=com_content&view=featured',
            'type' => 'component',
            'published' => 1,
            'home' => 1,
            'language' => 'ru-UA',
            'parent_id' => 0,
            'level' => 1,
            'lft' => 3,
            'rgt' => 4,
        ]);

        $ukHomeMenus = Menu::home('uk-UA')->get();
        $this->assertCount(1, $ukHomeMenus);
        $this->assertEquals('uk-UA', $ukHomeMenus->first()->language);

        $ruHomeMenus = Menu::home('ru-UA')->get();
        $this->assertCount(1, $ruHomeMenus);
        $this->assertEquals('ru-UA', $ruHomeMenus->first()->language);
    }
}

