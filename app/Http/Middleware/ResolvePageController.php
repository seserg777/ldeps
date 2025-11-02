<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Menu\Menu;
use App\Http\Controllers\Web\ContentController;
use App\Http\Controllers\Web\BannerController;
use App\Http\Controllers\Web\ProductController;

class ResolvePageController
{
    /**
     * Handle an incoming request and route to appropriate controller based on menu item type.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only process routes with {path} parameter
        if (!$request->route('path')) {
            return $next($request);
        }

        $path = $request->route('path');
        
        // Find menu item by alias
        $menuItem = Menu::where('alias', $path)
            ->where('published', 1)
            ->first();

        if (!$menuItem) {
            return $next($request);
        }

        // Parse link parameters to determine component type
        $linkParams = $this->parseLinkParams($menuItem->link);
        $componentType = $this->getComponentType($linkParams);

        // Route to appropriate controller based on component type
        switch ($componentType) {
            case 'ContentList':
                return redirect()->route('content.index');
                
            case 'Content':
                if (isset($linkParams['id'])) {
                    // Store content info in request for controller
                    $request->attributes->set('content_id', $linkParams['id']);
                    $request->attributes->set('seo_path', $path);
                    $request->attributes->set('menu_item', $menuItem);
                }
                break;
                
            case 'ExussalebannerList':
                return redirect()->route('banners.index');
                
            case 'Exussalebanner':
                if (isset($linkParams['id'])) {
                    return redirect()->route('banners.show', $linkParams['id']);
                }
                break;
                
            case 'Jshopping':
                // JСhopping pages - handle categories, products, etc.
                // Store jshopping info in request for controller
                $request->attributes->set('jshopping_params', $linkParams);
                $request->attributes->set('menu_item', $menuItem);
                break;
        }

        // Continue to default handler if no specific controller matched
        return $next($request);
    }

    /**
     * Parse link parameters from Joomla-style URL.
     *
     * @param string $link
     * @return array
     */
    private function parseLinkParams(string $link): array
    {
        $params = [];
        
        // Parse query string from link
        if (strpos($link, '?') !== false) {
            $queryString = substr($link, strpos($link, '?') + 1);
            parse_str($queryString, $params);
        }
        
        return $params;
    }

    /**
     * Get component type from link parameters.
     *
     * @param array $params
     * @return string
     */
    private function getComponentType(array $params): string
    {
        $option = $params['option'] ?? '';
        $view = $params['view'] ?? '';
        
        // Map Joomla component types to our component names
        if ($option === 'com_content') {
            if ($view === 'featured' || $view === 'category') {
                return 'ContentList';
            }
            if ($view === 'article') {
                return 'Content';
            }
        }
        
        if ($option === 'com_exussalebanner') {
            if ($view === 'exussalebanner') {
                return 'Exussalebanner';
            }
            if ($view === 'exussalebanners') {
                return 'ExussalebannerList';
            }
        }
        
        if ($option === 'com_jshopping') {
            return 'Jshopping';
        }
        
        return 'default';
    }
}

