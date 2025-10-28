<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Repositories\ProductRepository;
use App\Services\SearchService;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\SearchRequest;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    protected ProductRepository $productRepository;
    protected SearchService $searchService;

    public function __construct(ProductRepository $productRepository, SearchService $searchService)
    {
        $this->productRepository = $productRepository;
        $this->searchService = $searchService;
    }

    /**
     * Get products with filters.
     *
     * @param ProductFilterRequest $request
     * @return JsonResponse
     */
    public function index(ProductFilterRequest $request): JsonResponse
    {
        $filters = $request->validatedWithDefaults();
        
        $products = $this->productRepository
            ->getPublishedProducts($filters)
            ->paginate($filters['per_page']);

        return response()->json([
            'data' => ProductResource::collection($products->items()),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    /**
     * Search products.
     *
     * @param SearchRequest $request
     * @return JsonResponse
     */
    public function search(SearchRequest $request): JsonResponse
    {
        $results = $this->searchService->globalSearch(
            $request->validated()['q'],
            10
        );

        return response()->json([
            'products' => ProductResource::collection($results['products']),
            'categories' => $results['categories'],
            'manufacturers' => $results['manufacturers'],
        ]);
    }

    /**
     * Get product by path.
     *
     * @param string $path
     * @return JsonResponse
     */
    public function show(string $path): JsonResponse
    {
        $product = $this->productRepository->getByPath($path);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'data' => new ProductResource($product)
        ]);
    }
}
