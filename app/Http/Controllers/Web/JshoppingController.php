<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use Illuminate\Http\JsonResponse;

class JshoppingController extends Controller
{
    public function category(int $id): JsonResponse
    {
        $category = Category::active()->findOrFail($id);

        // Load direct children and their children
        $children = Category::active()
            ->where('category_parent_id', $category->category_id)
            ->ordered()
            ->withLocaleFields()
            ->with(['children' => function ($q) {
                $q->active()->ordered()->withLocaleFields()->with([
                    'children' => function ($qq) {
                        $qq->active()->ordered()->withLocaleFields();
                    },
                    'complexes' => function ($qc) {
                        $qc->active()->ordered()->withLocaleFields();
                    }
                ]);
            }])
            ->get()
            ->map(function ($child) {
                return [
                    'id' => $child->category_id,
                    'name' => $child->name,
                    'alias' => $child->alias,
                    'url' => route('category.show', $child->full_path),
                    'complexes' => $child->complexes->map(function ($c) {
                        return [
                            'id' => $c->complex_id,
                            'name' => $c->name,
                            'url' => $c->complex_url,
                        ];
                    })->toArray(),
                    'children' => $child->children->map(function ($g) {
                        return [
                            'id' => $g->category_id,
                            'name' => $g->name,
                            'alias' => $g->alias,
                            'url' => route('category.show', $g->full_path),
                            'children' => $g->children->map(function ($gg) {
                                return [
                                    'id' => $gg->category_id,
                                    'name' => $gg->name,
                                    'alias' => $gg->alias,
                                    'url' => route('category.show', $gg->full_path),
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                ];
            })->toArray();

        return response()->json([
            'category' => [
                'category_id' => $category->category_id,
                'category_parent_id' => $category->category_parent_id,
                'name' => $category->name,
                'alias' => $category->alias,
                'full_path' => $category->full_path,
            ],
            'children' => $children,
        ]);
    }
}
