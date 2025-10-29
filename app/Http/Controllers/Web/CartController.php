<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use App\Models\Product\Product;
use App\Models\Category\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the cart page.
     *
     * @return View
     */
    public function index(): View
    {
        // Get cart from session
        $cart = session('cart', []);
        
        // Get products from cart
        $products = Product::published()
            ->whereIn('product_id', array_keys($cart))
            ->get()
            ->map(function($product) use ($cart) {
                $product->quantity = $cart[$product->product_id] ?? 1;
                return $product;
            });

        // Calculate totals
        $subtotal = $products->sum(function($product) {
            return $product->product_price * $product->quantity;
        });

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

        return view('front.cart.index', compact('products', 'subtotal', 'categories'));
    }

    /**
     * Add product to cart.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        
        if (!$productId) {
            return response()->json(['success' => false, 'message' => 'Product ID is required']);
        }

        // Check if product exists
        $product = Product::published()->find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        // Get current cart
        $cart = session('cart', []);
        
        // Add to cart
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }
        
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'count' => array_sum($cart)
        ]);
    }

    /**
     * Update product quantity in cart.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);
        
        if (!$productId) {
            return response()->json(['success' => false, 'message' => 'Product ID is required']);
        }

        // Get current cart
        $cart = session('cart', []);
        
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }
        
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'count' => array_sum($cart)
        ]);
    }

    /**
     * Remove product from cart.
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

        // Get current cart
        $cart = session('cart', []);
        
        // Remove from cart
        unset($cart[$productId]);
        
        session(['cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart',
            'count' => array_sum($cart)
        ]);
    }

    /**
     * Clear cart.
     *
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        session(['cart' => []]);

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'count' => 0
        ]);
    }

    /**
     * Get cart count.
     *
     * @return JsonResponse
     */
    public function count(): JsonResponse
    {
        $cart = session('cart', []);
        
        return response()->json(['count' => array_sum($cart)]);
    }

    /**
     * Get cart data for modal.
     *
     * @return JsonResponse
     */
    public function modal(): JsonResponse
    {
        $cart = session('cart', []);
        
        // Get products from cart
        $products = Product::published()
            ->whereIn('product_id', array_keys($cart))
            ->get()
            ->map(function($product) use ($cart) {
                return [
                    'product_id' => $product->product_id,
                    'name' => $product->name,
                    'price' => $product->product_price,
                    'quantity' => $cart[$product->product_id] ?? 1,
                    'image' => $product->product_image ?: '/images/no-image.svg'
                ];
            });

        $total = $products->sum(function($product) {
            return $product['price'] * $product['quantity'];
        });

        return response()->json([
            'items' => $products,
            'count' => array_sum($cart),
            'total' => $total
        ]);
    }
}
