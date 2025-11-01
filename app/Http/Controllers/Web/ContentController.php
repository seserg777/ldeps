<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\BuildsMenus;
use App\Models\JContent;
use App\Helpers\MenuRenderer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContentController extends Controller
{
    use BuildsMenus;

    /**
     * Display a listing of articles
     */
    public function index(Request $request): View
    {
        $language = app()->getLocale();
        $currentLang = $language === 'ru' ? 'ru-UA' : 'uk-UA';
        $perPage = 12; // Number of articles per page
        $page = $request->get('page', 1);

        // For now, return empty array - will be implemented when we have articles table
        $articles = [];
        $pagination = [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => 0,
            'last_page' => 1,
            'has_more' => false
        ];

        // Get menus and modules for this page
        $activeMenuId = MenuRenderer::detectActiveMenuId();
        $menuData = MenuRenderer::getMenusForPage($activeMenuId);
        $menuTopHtml = $menuData['menuTopHtml'];
        $menuMainHtml = $menuData['menuMainHtml'];

        // Site metadata
        $siteName = config('app.name', 'LDEPS');
        $siteDescription = 'Laboratory and Diagnostic Equipment, Parts and Supplies';
        $language = app()->getLocale();

        // Prepare data for the component
        $pageData = [
            'menuTopHtml' => $menuTopHtml,
            'menuMainHtml' => $menuMainHtml,
            'siteName' => $siteName,
            'siteDescription' => $siteDescription,
            'language' => $language,
            'componentType' => 'ContentList',
            'menuItem' => [
                'title' => 'Статьи',
                'alias' => 'articles',
                'path' => 'articles',
                'link' => null,
                'level' => 1
            ],
            'linkParams' => [
                'option' => 'com_content',
                'view' => 'content'
            ],
            'additionalData' => [
                'articles' => $articles,
                'pagination' => $pagination
            ]
        ];

        return view('front.page', compact('pageData', 'menuTopHtml', 'menuMainHtml'));
    }

    /**
     * Display the specified article
     */
    public function show(Request $request, $id): View
    {
        // Validate and convert ID to integer
        $id = (int) $id;
        if ($id <= 0) {
            abort(404, 'Invalid article ID');
        }

        $language = app()->getLocale();

        // Load article from vjprf_content
        $record = JContent::published()->find($id);
        if (!$record) {
            abort(404, 'Article not found');
        }

        $article = [
            'id' => $record->id,
            'title' => $record->title,
            'alias' => $record->alias,
            'description' => $record->introtext,
            'fulltext' => $record->fulltext,
            'image' => null,
            'created' => optional($record->created)->format('Y-m-d H:i:s'),
        ];

        // Get menus and modules for this page
        $activeMenuId = MenuRenderer::detectActiveMenuId();
        $menuData = MenuRenderer::getMenusForPage($activeMenuId);
        $menuTopHtml = $menuData['menuTopHtml'];
        $menuMainHtml = $menuData['menuMainHtml'];

        // Site metadata
        $siteName = config('app.name', 'LDEPS');
        $siteDescription = 'Laboratory and Diagnostic Equipment, Parts and Supplies';
        $language = app()->getLocale();

        // Prepare data for the component
        $pageData = [
            'menuTopHtml' => $menuTopHtml,
            'menuMainHtml' => $menuMainHtml,
            'siteName' => $siteName,
            'siteDescription' => $siteDescription,
            'language' => $language,
            'componentType' => 'Content',
            'menuItem' => [
                'title' => $article['title'],
                'alias' => $article['alias'] ?: 'article-'.$id,
                'path' => 'articles',
                'link' => null,
                'level' => 1
            ],
            'linkParams' => [
                'option' => 'com_content',
                'view' => 'article',
                'id' => $id
            ],
            'additionalData' => [
                'article' => $article
            ]
        ];

        return view('front.page', compact('pageData', 'menuTopHtml', 'menuMainHtml'));
    }
}
