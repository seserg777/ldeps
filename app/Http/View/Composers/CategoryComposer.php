<?php

namespace App\Http\View\Composers;

use App\Models\Category\Category;
use Illuminate\View\View;

class CategoryComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Get root categories with their subcategories and sub-subcategories for navigation
        $categories = Category::active()
            ->root()
            ->ordered()
            ->withCount('products')
            ->with(['subcategories' => function($query) {
                $query->active()->ordered()->with(['parent', 'subcategories' => function($subQuery) {
                    $subQuery->active()->ordered();
                }]);
            }])
            ->get();

        // Debug: Log categories count
        \Log::info('CategoryComposer: Loaded ' . $categories->count() . ' categories');
        \Log::info('CategoryComposer: View name: ' . $view->getName());

        $view->with('categories', $categories);
    }
}
