<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BuildsMenus;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    use BuildsMenus;

    /**
     * Return server-rendered HTML for a menu type.
     */
    public function html(Request $request, string $menutype)
    {
        $maxLevels = (int) $request->get('maxLevels', 4);
        $language = $request->get('language');

        $menus = $this->buildMenus([$menutype]);
        $items = $menus[$menutype] ?? [];

        return view('share.menu.html', [
            'items' => $items,
            'maxLevels' => $maxLevels,
            'language' => $language,
        ]);
    }
}


