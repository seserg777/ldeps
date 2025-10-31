<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BuildsMenus;
use App\Models\Exussalebanner;
use Illuminate\View\View;

class HomeController extends Controller
{
    use BuildsMenus;

    /**
     * Homepage
     */
    public function index(): View
    {
        // Build required menus. We can add more types later as needed.
        $menus = $this->buildMenus(['main-menu-add', 'mainmenu-rus']);
        $menuItemsTop = $menus['main-menu-add'] ?? [];
        $menuItemsMain = $menus['mainmenu-rus'] ?? [];

        $siteName = config('app.name', 'Интернет-магазин');
        $siteDescription = 'Лучшие товары по доступным ценам';
        $language = app()->getLocale();

        // Render SSR menu HTML for Blade-only frontend
        $menuTopHtml = view('share.menu.html', [
            'items' => $menuItemsTop,
            'language' => $language,
            'maxLevels' => 4,
        ])->render();

        $menuMainHtml = view('share.menu.html', [
            'items' => $menuItemsMain,
            'language' => $language,
            'maxLevels' => 4,
        ])->render();

        // Placeholder homepage content area (can be replaced by widgets/partials)
        $homepageHtml = '';

        return view('front.homepage', compact(
            'menuTopHtml',
            'menuMainHtml',
            'homepageHtml',
            'siteName',
            'siteDescription',
            'language'
        ));
    }

    /**
     * Show page by SEO path
     */
    public function showPage(string $path)
    {
        // Find menu item by alias/path
        $menuItem = \App\Models\Menu\Menu::where('alias', $path)
            ->where('published', 1)
            ->first();

        if (!$menuItem) {
            abort(404, 'Page not found');
        }

        // Parse link parameters to determine component
        $linkParams = $this->parseLinkParams($menuItem->link);
        $componentType = $this->getComponentType($linkParams);

        // Redirect to specific controllers based on component type (keep SEO URLs for content pages)
        if ($componentType === 'ContentList') {
            return redirect()->route('content.index');
        } elseif ($componentType === 'Content' && isset($linkParams['id'])) {
            // Keep /about.html (or any SEO path) and render article here
            $record = \App\Models\JContent::published()->find((int) $linkParams['id']);
            if (!$record) {
                abort(404, 'Article not found');
            }

            $menus = $this->buildMenus(['main-menu-add', 'mainmenu-rus']);
            $menuItemsTop = $menus['main-menu-add'] ?? [];
            $menuItemsMain = $menus['mainmenu-rus'] ?? [];

            $siteName = config('app.name', 'Интернет-магазин');
            $siteDescription = 'Лучшие товары по доступным ценам';
            $language = app()->getLocale();

            $pageData = [
                'menuItemsTop' => $menuItemsTop,
                'menuItemsMain' => $menuItemsMain,
                'siteName' => $siteName,
                'siteDescription' => $siteDescription,
                'language' => $language,
                'componentType' => 'Content',
                'menuItem' => [
                    'title' => $record->title,
                    'alias' => $record->alias,
                    'path' => $menuItem->alias,
                    'link' => $menuItem->link,
                    'level' => $menuItem->level,
                ],
                'linkParams' => $linkParams,
                'additionalData' => [
                    'article' => [
                        'id' => $record->id,
                        'title' => $record->title,
                        'alias' => $record->alias,
                        'description' => $record->introtext,
                        'fulltext' => $record->fulltext,
                        'image' => null,
                        'created' => optional($record->created)->format('Y-m-d H:i:s'),
                    ]
                ]
            ];

            return view('front.page', compact('pageData'));
        } elseif ($componentType === 'ExussalebannerList') {
            return redirect()->route('banners.index');
        } elseif ($componentType === 'Exussalebanner' && isset($linkParams['id'])) {
            return redirect()->route('banners.show', $linkParams['id']);
        }

        // For other types, show generic page
        $menus = $this->buildMenus(['main-menu-add', 'mainmenu-rus']);
        $menuItemsTop = $menus['main-menu-add'] ?? [];
        $menuItemsMain = $menus['mainmenu-rus'] ?? [];

        $siteName = config('app.name', 'Интернет-магазин');
        $siteDescription = 'Лучшие товары по доступным ценам';
        $language = app()->getLocale();

        $menuTopHtml = view('share.menu.html', [
            'items' => $menuItemsTop,
            'language' => $language,
            'maxLevels' => 4,
        ])->render();
        $menuMainHtml = view('share.menu.html', [
            'items' => $menuItemsMain,
            'language' => $language,
            'maxLevels' => 4,
        ])->render();

        $pageContentHtml = '';

        return view('front.page', compact('menuTopHtml', 'menuMainHtml', 'pageContentHtml', 'siteName', 'siteDescription', 'language'));
    }

    /**
     * Show individual banner page - redirect to BannerController
     */
    public function showBanner(string $category, int $id)
    {
        // Redirect to BannerController for proper handling
        return redirect()->to("/banner/{$id}");
    }

    /**
     * JSON meta for a page by SEO alias (used by Vue components during hydration)
     */
    public function pageMeta(string $path)
    {
        $menuItem = \App\Models\Menu\Menu::where('alias', $path)
            ->where('published', 1)
            ->first();

        if (!$menuItem) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $linkParams = $this->parseLinkParams($menuItem->link);
        $componentType = $this->getComponentType($linkParams);

        $payload = [
            'success' => true,
            'menuItem' => [
                'title' => $menuItem->title,
                'alias' => $menuItem->alias,
                'path' => $menuItem->alias,
                'link' => $menuItem->link,
                'level' => $menuItem->level,
            ],
            'linkParams' => $linkParams,
            'componentType' => $componentType,
        ];

        if ($componentType === 'Content' && isset($linkParams['id'])) {
            $record = \App\Models\JContent::published()->find((int) $linkParams['id']);
            if ($record) {
                $payload['additionalData']['article'] = [
                    'id' => $record->id,
                    'title' => $record->title,
                    'alias' => $record->alias,
                    'description' => $record->introtext,
                    'fulltext' => $record->fulltext,
                    'image' => null,
                    'created' => optional($record->created)->format('Y-m-d H:i:s'),
                ];
            }
        }

        return response()->json($payload);
    }

    /**
     * Parse link parameters
     */
    private function parseLinkParams($link)
    {
        if (!$link || !is_string($link)) {
            return null;
        }

        try {
            // Handle both full URLs and relative URLs
            if (strpos($link, 'http') === 0) {
                $url = parse_url($link);
            } else {
                // For relative URLs like "index.php?option=com_exussalebanner&view=exussalebanner"
                $url = parse_url('http://example.com/' . ltrim($link, '/'));
            }

            if (!isset($url['query'])) {
                return null;
            }

            parse_str($url['query'], $params);
            return $params;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Determine component type based on link parameters
     */
    private function getComponentType($params)
    {
        if (!$params || !isset($params['option'])) {
            return null;
        }

        switch ($params['option']) {
            case 'com_content':
                if (isset($params['view']) && $params['view'] === 'article') {
                    return 'Content';
                }
                return 'ContentList';
            case 'com_exussalebanner':
                // Check view parameter to determine component type
                if (isset($params['view']) && $params['view'] === 'exussalebanner') {
                    return 'ExussalebannerList';
                }
                return 'Exussalebanner';
                // Add more mappings as needed
            default:
                return null;
        }
    }
}
