<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BuildsMenus;
use App\Models\Exussalebanner;
use App\Helpers\MenuRenderer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    use BuildsMenus;

    /**
     * Display a listing of banners
     */
    public function index(Request $request): View
    {
        $language = app()->getLocale();
        $currentLang = $language === 'ru' ? 'ru-UA' : 'uk-UA';
        $perPage = 12; // Number of banners per page
        $page = $request->get('page', 1);
        
        $bannersQuery = Exussalebanner::published()->ordered();
        $totalBanners = $bannersQuery->count();
        $banners = $bannersQuery->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get()
            ->map(function($banner) use ($currentLang) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->{"title_{$currentLang}"} ?: $banner->title,
                    'alias' => $banner->{"alias_{$currentLang}"} ?: $banner->alias,
                    'description' => $banner->{"introtext_{$currentLang}"} ?: $banner->introtext,
                    'image' => $banner->{"image_{$currentLang}"} ?: $banner->image,
                    'link' => null, // Banners don't have direct links
                    'created' => $banner->created ? $banner->created->format('Y-m-d H:i:s') : null,
                    'sale_start' => $banner->sale_start,
                    'sale_end' => $banner->sale_end,
                ];
            });
        
        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $totalBanners,
            'last_page' => ceil($totalBanners / $perPage),
            'has_more' => $page < ceil($totalBanners / $perPage)
        ];

        // Get menus and modules for this page
        $activeMenuId = MenuRenderer::detectActiveMenuId();
        $menuData = MenuRenderer::getMenusForPage($activeMenuId);
        $menuTopHtml = $menuData['menuTopHtml'];
        $menuMainHtml = $menuData['menuMainHtml'];

        // Prepare data for the component
        $pageData = [
            'menuItemsTop' => $menuItemsTop,
            'menuItemsMain' => $menuItemsMain,
            'siteName' => $siteName,
            'siteDescription' => $siteDescription,
            'language' => $language,
            'componentType' => 'ExussalebannerList',
            'menuItem' => [
                'title' => 'Акции',
                'alias' => 'promo',
                'path' => 'promo',
                'link' => null,
                'level' => 1
            ],
            'linkParams' => [
                'option' => 'com_exussalebanner',
                'view' => 'exussalebanner'
            ],
            'additionalData' => [
                'banners' => $banners,
                'pagination' => $pagination
            ]
        ];

        return view('front.page', compact('pageData', 'menuTopHtml', 'menuMainHtml'));
    }

    /**
     * Display the specified banner
     */
    public function show(Request $request, $id): View
    {
        // Validate and convert ID to integer
        $id = (int) $id;
        if ($id <= 0) {
            abort(404, 'Invalid banner ID');
        }

        // Find the banner
        $banner = Exussalebanner::published()->find($id);
        
        if (!$banner) {
            abort(404, 'Banner not found');
        }

        // Get language and prepare banner data
        $language = app()->getLocale();
        $currentLang = $language === 'ru' ? 'ru-UA' : 'uk-UA';
        
        $bannerData = [
            'id' => $banner->id,
            'title' => $banner->{"title_{$currentLang}"} ?: $banner->title,
            'alias' => $banner->{"alias_{$currentLang}"} ?: $banner->alias,
            'description' => $banner->{"introtext_{$currentLang}"} ?: $banner->introtext,
            'fulltext' => $banner->{"fulltext_{$currentLang}"} ?: $banner->fulltext,
            'image' => $banner->{"image_{$currentLang}"} ?: $banner->image,
            'sale_start' => $banner->sale_start,
            'sale_end' => $banner->sale_end,
            'created' => $banner->created ? $banner->created->format('Y-m-d H:i:s') : null,
        ];

        // Get menus and modules for this page
        $activeMenuId = MenuRenderer::detectActiveMenuId();
        $menuData = MenuRenderer::getMenusForPage($activeMenuId);
        $menuTopHtml = $menuData['menuTopHtml'];
        $menuMainHtml = $menuData['menuMainHtml'];

        // Prepare data for the component
        $pageData = [
            'menuItemsTop' => $menuItemsTop,
            'menuItemsMain' => $menuItemsMain,
            'siteName' => $siteName,
            'siteDescription' => $siteDescription,
            'language' => $language,
            'componentType' => 'Exussalebanner',
            'menuItem' => [
                'title' => $bannerData['title'],
                'alias' => $bannerData['alias'],
                'path' => 'promo',
                'link' => null,
                'level' => 1
            ],
            'linkParams' => [
                'option' => 'com_exussalebanner',
                'id' => $id
            ],
            'additionalData' => [
                'banner' => $bannerData
            ]
        ];

        return view('front.page', compact('pageData', 'menuTopHtml', 'menuMainHtml'));
    }
}
