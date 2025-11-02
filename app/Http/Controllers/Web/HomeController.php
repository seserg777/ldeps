<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BuildsMenus;
use App\Http\Controllers\Web\ContentController;
use App\Models\Exussalebanner;
use App\Models\Menu\Menu;
use App\Models\JContent;
use App\Helpers\MenuRenderer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    use BuildsMenus;

    /**
     * Homepage
     */
    public function index(): View
    {
        // Get menus and modules for homepage
        $activeMenuId = MenuRenderer::detectActiveMenuId();
        $pageModules = MenuRenderer::getModulesForPage($activeMenuId, true); // Include global modules
        $menuModules = MenuRenderer::getMenuModules($pageModules);
        
        // Render menus for each module
        $renderedMenus = MenuRenderer::renderMenuModules($menuModules);

        // Placeholder homepage content area (can be replaced by widgets/partials)
        $homepageHtml = '';
        
        // Site metadata
        $siteName = config('app.name', 'DEPS');
        $siteDescription = 'Laboratory and Diagnostic Equipment, Parts and Supplies';
        $language = app()->getLocale();

        return view('front.homepage', compact(
            'activeMenuId',
            'menuModules',
            'renderedMenus',
            'homepageHtml',
            'siteName',
            'siteDescription',
            'language'
        ));
    }

    /**
     * Show generic page by SEO path.
     * Note: ResolvePageController middleware may have set attributes for specific content types.
     */
    public function showPage(Request $request, string $path)
    {
        // Check if middleware resolved this to a content page
        if ($request->attributes->has('content_id')) {
            return app(ContentController::class)->show(
                $request->attributes->get('content_id'),
                $request->attributes->get('seo_path')
            );
        }
        
        // Find menu item by alias/path
        $menuItem = Menu::where('alias', $path)
            ->where('published', 1)
            ->first();

        if (!$menuItem) {
            abort(404, 'Page not found');
        }

        // Parse link parameters
        $linkParams = $this->parseLinkParams($menuItem->link);

        // Get menus and modules for this page
        $activeMenuId = MenuRenderer::detectActiveMenuId();
        $pageModules = MenuRenderer::getModulesForPage($activeMenuId, true);
        $menuModules = MenuRenderer::getMenuModules($pageModules);
        $renderedMenus = MenuRenderer::renderMenuModules($menuModules);

        $siteName = config('app.name', 'Интернет-магазин');
        $siteDescription = 'Лучшие товары по доступным ценам';
        $language = app()->getLocale();

        $pageData = [
            'renderedMenus' => $renderedMenus,
            'activeMenuId' => $activeMenuId,
            'siteName' => $siteName,
            'siteDescription' => $siteDescription,
            'language' => $language,
            'componentType' => 'page',
            'menuItem' => [
                'title' => $menuItem->title,
                'alias' => $menuItem->alias,
                'path' => $menuItem->alias,
                'link' => $menuItem->link,
                'level' => $menuItem->level,
            ],
            'linkParams' => $linkParams,
        ];

        return view('front.page', compact('pageData', 'activeMenuId', 'renderedMenus'));
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
        $menuItem = Menu::where('alias', $path)
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
            $record = JContent::published()->find((int) $linkParams['id']);
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
