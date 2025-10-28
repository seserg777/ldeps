<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class WishlistController extends Controller
{
    /**
     * Display the wishlist page.
     *
     * @return View
     */
    public function index(): View
    {
        // Get wishlist from session
        $wishlist = session('wishlist', []);
        
        // Get products from wishlist
        $products = Product::published()
            ->whereIn('product_id', $wishlist)
            ->get();

        // Get categories for navigation
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

        return view('wishlist.index', compact('products', 'categories'));
    }

    /**
     * Add product to wishlist.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');
        
        if (!$productId) {
            return response()->json(['success' => false, 'message' => 'Product ID is required']);
        }

        // Check if product exists
        $product = Product::published()->find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        // Get current wishlist
        $wishlist = session('wishlist', []);
        
        // Add to wishlist if not already there
        if (!in_array($productId, $wishlist)) {
            $wishlist[] = $productId;
            session(['wishlist' => $wishlist]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist',
            'count' => count($wishlist)
        ]);
    }

    /**
     * Remove product from wishlist.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function remove(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');
        
        if (!$productId) {
            return response()->json(['success' => false, 'message' => 'Product ID is required']);
        }

        // Get current wishlist
        $wishlist = session('wishlist', []);
        
        // Remove from wishlist
        $wishlist = array_filter($wishlist, function($id) use ($productId) {
            return $id != $productId;
        });
        
        session(['wishlist' => array_values($wishlist)]);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist',
            'count' => count($wishlist)
        ]);
    }

    /**
     * Get wishlist count.
     *
     * @return JsonResponse
     */
    public function count(): JsonResponse
    {
        $wishlist = session('wishlist', []);
        
        return response()->json(['count' => count($wishlist)]);
    }
}
