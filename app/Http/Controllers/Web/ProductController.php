<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Category\Category;
use App\Models\Manufacturer;
use App\Models\ProductExtraFieldValue;
use App\Models\Complex;
use App\Services\Product\ProductService;
use App\Services\Category\CategoryService;
use App\Services\Product\SearchService;
use App\Repositories\ProductRepository;
use App\DTOs\ProductFilterDTO;
use App\Events\ProductViewed;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\SearchRequest;
use App\Helpers\MenuRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\Concerns\BuildsMenus;

class ProductController extends Controller
{
    use BuildsMenus;
    protected ProductService $productService;
    protected CategoryService $categoryService;
    protected SearchService $searchService;
    protected ProductRepository $productRepository;

    public function __construct(
        ProductService $productService,
        CategoryService $categoryService,
        SearchService $searchService,
        ProductRepository $productRepository
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->searchService = $searchService;
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of products with filtering and sorting.
     *
     * @param ProductFilterRequest $request
     * @return View|JsonResponse
     */
    public function index(ProductFilterRequest $request)
    {

        $query = Product::published();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Price range filter
        if ($request->filled('price_min') || $request->filled('price_max')) {
            $minPrice = $request->price_min ? (float) $request->price_min : 0;
            $maxPrice = $request->price_max ? (float) $request->price_max : 999999;
            $query->priceRange($minPrice, $maxPrice);
        }

        // Manufacturer filtering is handled by f parameter only

        // Special price filter
        if ($request->filled('special_price')) {
            $query->specialPrice();
        }

        // Category filter
        if ($request->filled('category')) {
            $category = Category::find($request->category);
            if ($category) {
                $categoryIds = $this->getCategoryAndSubcategoryIds($category);
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('vjprf_jshopping_products_to_categories.category_id', $categoryIds);
                });
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'date_added');
        $sortDirection = $request->get('direction', 'desc');

        switch ($sortBy) {
            case 'price_asc':
                $query->priceAsc();
                break;
            case 'price_desc':
                $query->priceDesc();
                break;
            case 'popularity':
                $query->popular();
                break;
            case 'rating':
                $query->byRating();
                break;
            case 'name':
                $query->orderBy('name_uk-UA', $sortDirection);
                break;
            default:
                $query->orderBy('product_date_added', 'desc');
                break;
        }

        // Get manufacturers for filter with product counts
        $manufacturers = Manufacturer::published()
            ->ordered()
            ->withCount(['products' => function ($query) {
                $query->published();
            }])
            ->having('products_count', '>', 0)
            ->get();

        // Get price range for filter
        $priceRange = Product::published()
            ->selectRaw('MIN(product_price) as min_price, MAX(product_price) as max_price')
            ->first();

        // Pagination
        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage)->withQueryString();

        // For AJAX requests, return JSON with HTML
        if ($request->ajax()) {
            return response()->json([
                'products' => $products,
                'products_html' => view('front.products.partials.product-grid', compact('products'))->render(),
                'filters' => [
                    'manufacturers' => $manufacturers,
                    'price_range' => $priceRange,
                ]
            ]);
        }

        // Get root categories with their subcategories and sub-subcategories for navigation
        $categories = Category::active()
            ->root()
            ->ordered()
            ->withCount('products')
            ->with(['subcategories' => function ($query) {
                $query->active()->ordered()->with(['parent', 'subcategories' => function ($subQuery) {
                    $subQuery->active()->ordered();
                }]);
            }])
            ->get();

        // Check if Vue version is requested
        if ($request->get('vue') === '1' || $request->routeIs('products.vue')) {
            return view('front.products.index-vue', compact('products', 'manufacturers', 'priceRange', 'categories'));
        }

        return view('front.products.index-new', compact('products', 'manufacturers', 'priceRange', 'categories'));
    }



    /**
     * Display the specified product.
     *
     * @param int $id
     * @return View
     */
    public function show($id): View
    {
        $id = (int) $id;
        if ($id <= 0) {
            abort(404, 'Product not found');
        }

        $product = Product::published()
            ->with(['manufacturer', 'categories', 'productCharacteristics.extraField', 'productCharacteristics.extraFieldValue', 'productAttributes'])
            ->findOrFail($id);


        // Increment hits counter
        $product->increment('hits');

        // Get menus and modules for this page
        $menuData = MenuRenderer::getMenusForPage(null);
        $menuTopHtml = $menuData['menuTopHtml'];
        $menuMainHtml = $menuData['menuMainHtml'];
        $language = app()->getLocale();

        $pageData = [
            'language' => $language,
            'siteName' => config('app.name', 'Site'),
            'siteDescription' => $product->short_description,
            'menuItem' => [
                'id' => null,
                'title' => $product->name,
                'alias' => $product->alias,
                'path' => $product->full_path,
            ],
            'componentType' => 'Product',
            'additionalData' => [
                'product' => $product
            ],
        ];

        $activeMenuId = $menuData['activeMenuId'];
        $componentClass = 'product';

        return view('front.page', compact('pageData', 'activeMenuId', 'componentClass', 'menuTopHtml', 'menuMainHtml', 'product'));
    }

    /**
     * Display product by hierarchical path.
     *
     * @param string $path
     * @return View
     */
    public function showByPath(string $path): View
    {
        // Split path into segments
        $pathSegments = explode('/', $path);
        $productAlias = end($pathSegments);

        // Remove product alias from path to get category path
        array_pop($pathSegments);
        $categoryPath = implode('/', $pathSegments);

        // Find product by alias
        $locale = app()->getLocale();
        $localeMap = [
            'uk' => 'uk-UA',
            'ru' => 'ru-UA',
            'en' => 'en-GB'
        ];
        $dbLocale = $localeMap[$locale] ?? 'uk-UA';
        $aliasField = "alias_{$dbLocale}";

        $product = Product::published()
            ->with(['manufacturer', 'categories', 'productCharacteristics.extraField', 'productCharacteristics.extraFieldValue'])
            ->where($aliasField, $productAlias)
            ->first();

        // If not found by alias, try to find by product ID (fallback for products without aliases)
        if (!$product && is_numeric($productAlias)) {
            $product = Product::published()
                ->with(['manufacturer', 'categories', 'productCharacteristics.extraField', 'productCharacteristics.extraFieldValue'])
                ->where('product_id', $productAlias)
                ->first();
        }

        if (!$product) {
            abort(404, 'Product not found');
        }


        // If there's a category path, verify it matches the product's category
        if ($categoryPath) {
            $category = $product->categories()->first();
            if (!$category || $category->full_path !== $categoryPath) {
                // Redirect to correct path
                return redirect()->route('products.show-by-path', $product->full_path, 301);
            }
        }

        // Increment hits counter
        $product->increment('hits');

        // Build menus for page layout
        $menus = $this->buildMenus(['main-menu-add', 'mainmenu-rus']);
        $menuItemsTop = $menus['main-menu-add'] ?? [];
        $menuItemsMain = $menus['mainmenu-rus'] ?? [];
        $language = app()->getLocale();

        // Render menu HTML
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

        $pageData = [
            'language' => $language,
            'siteName' => config('app.name', 'Site'),
            'siteDescription' => $product->short_description,
            'menuItem' => [
                'id' => null,
                'title' => $product->name,
                'alias' => $product->alias,
                'path' => $product->full_path,
            ],
            'componentType' => 'Product',
            'additionalData' => [
                'product' => $product
            ],
        ];

        $activeMenuId = $menuData['activeMenuId'];
        $componentClass = 'product';

        return view('front.page', compact('pageData', 'activeMenuId', 'componentClass', 'menuTopHtml', 'menuMainHtml', 'product'));
    }

    /**
     * Display products by category path.
     *
     * @param string $path
     * @return View
     */
    public function category(string $path): View
    {
        // Split path into segments
        $pathSegments = explode('/', $path);
        $lastSegment = end($pathSegments);

        // Find category by the last segment (leaf category)
        $category = Category::active()
            ->where(function ($query) use ($lastSegment) {
                $query->where('alias_uk-UA', $lastSegment)
                      ->orWhere('alias_ru-UA', $lastSegment)
                      ->orWhere('alias_en-GB', $lastSegment);
            })
            ->firstOrFail();

        // Verify the full path matches the category hierarchy
        $expectedPath = $category->full_path;
        if ($path !== $expectedPath) {
            // Redirect to correct path
            return redirect()->route('category.show', $expectedPath, 301);
        }

        // Get products for this category and all its subcategories
        $categoryIds = $this->getCategoryAndSubcategoryIds($category);

        $query = Product::published()
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('vjprf_jshopping_products_to_categories.category_id', $categoryIds);
            });

        // Handle filter parameter f (e.g., ?f=l16 for extra fields, ?f=4 for manufacturer)
        $filterParam = request()->get('f');
        $filteredExtraFieldValue = null;
        $filteredManufacturer = null;

        if ($filterParam) {
            if (str_starts_with($filterParam, 'l')) {
                // Extra field filter (e.g., ?f=l16)
                $extraFieldValueId = (int) substr($filterParam, 1); // Extract number from l16 -> 16

                // Get extra field value info
                $filteredExtraFieldValue = ProductExtraFieldValue::with('extraField')->find($extraFieldValueId);

                if ($filteredExtraFieldValue) {
                    // Filter products by characteristic
                    $query->whereHas('productCharacteristics', function ($q) use ($extraFieldValueId) {
                        $q->where('extra_field_value', $extraFieldValueId);
                    });
                }
            } else {
                // Manufacturer filter - handle both single and multiple values
                // Single: ?f=4, Multiple: ?f=4,6,8
                $manufacturerIds = array_map('intval', explode(',', $filterParam));

                if (count($manufacturerIds) === 1 && $manufacturerIds[0] > 0) {
                    // Single manufacturer - get manufacturer info for display
                    $filteredManufacturer = Manufacturer::find($manufacturerIds[0]);
                }

                // Filter products by manufacturer(s)
                $query->whereIn('product_manufacturer_id', $manufacturerIds);
            }
        }

        // Get manufacturers for filter with product counts for current category
        // First, get only manufacturers that have products in current category
        $manufacturersInCategory = Manufacturer::published()
            ->whereHas('products', function ($query) use ($categoryIds) {
                $query->published()
                    ->whereHas('categories', function ($q) use ($categoryIds) {
                        $q->whereIn('vjprf_jshopping_products_to_categories.category_id', $categoryIds);
                    });
            })
            ->ordered()
            ->select([
                'vjprf_jshopping_manufacturers.*',
                'vjprf_jshopping_manufacturers.name_uk-UA as name_uk_UA',
                'vjprf_jshopping_manufacturers.name_ru-UA as name_ru_UA',
                'vjprf_jshopping_manufacturers.name_en-GB as name_en_GB'
            ])
            ->get();

        $tiandyBefore = $manufacturersInCategory->where('manufacturer_id', 269)->first();
        \Log::info('Manufacturers in category before calculation:', [
            'count' => $manufacturersInCategory->count(),
            'tiandy_before' => $tiandyBefore ? (array) $tiandyBefore : null
        ]);

        // Now calculate counts with current filters applied
        $manufacturers = collect();

        foreach ($manufacturersInCategory as $manufacturer) {
            // Calculate product count for this manufacturer with current filters
            $countQuery = Product::published()
                ->where('product_manufacturer_id', $manufacturer->manufacturer_id)
                ->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('vjprf_jshopping_products_to_categories.category_id', $categoryIds);
                });

            // Apply extra field filter if present
            if ($filterParam && str_starts_with($filterParam, 'l')) {
                $extraFieldValueId = (int) substr($filterParam, 1);
                $countQuery->whereHas('productCharacteristics', function ($q) use ($extraFieldValueId) {
                    $q->where('extra_field_value', $extraFieldValueId);
                });
            }

            // Apply price range filter if present
            if (request()->filled('price_min') || request()->filled('price_max')) {
                $minPrice = request()->price_min ? (float) request()->price_min : 0;
                $maxPrice = request()->price_max ? (float) request()->price_max : 999999;
                $countQuery->whereBetween('product_price', [$minPrice, $maxPrice]);
            }

            // Apply special price filter if present
            if (request()->filled('special_price')) {
                $countQuery->where('product_price', '<', 1000);
            }

            // Calculate the count for this manufacturer
            $count = $countQuery->count();

            // Debug: Log individual manufacturer count
            if ($manufacturer->manufacturer_id == 269) { // Tiandy
                \Log::info('Tiandy manufacturer count calculation:', [
                    'manufacturer_id' => $manufacturer->manufacturer_id,
                    'filterParam' => $filterParam,
                    'count' => $count,
                    'sql' => $countQuery->toSql(),
                    'bindings' => $countQuery->getBindings()
                ]);
            }

            // Only add manufacturers with products
            if ($count > 0) {
                // Create a simple object without any relationships
                $newManufacturer = (object) [
                    'manufacturer_id' => $manufacturer->manufacturer_id,
                    'name' => $manufacturer->name,
                    'alias' => $manufacturer->alias,
                    'manufacturer_logo' => $manufacturer->manufacturer_logo,
                    'manufacturer_url' => $manufacturer->manufacturer_url,
                    'manufacturer_publish' => $manufacturer->manufacturer_publish,
                    'ordering' => $manufacturer->ordering,
                    'products_count' => $count
                ];
                $manufacturers->push($newManufacturer);
            }
        }

        // Debug: Log manufacturer counts
        \Log::info('Manufacturer counts with filters:', [
            'filterParam' => $filterParam,
            'categoryIds' => $categoryIds,
            'manufacturer_counts' => $manufacturers->map(function ($m) {
                return ['id' => $m->manufacturer_id, 'name' => $m->name, 'count' => $m->products_count];
            })->toArray()
        ]);

        // Debug: Check if Tiandy count is correct in final result
        $tiandyInFinal = $manufacturers->where('manufacturer_id', 269)->first();
        if ($tiandyInFinal) {
            \Log::info('Tiandy in final manufacturers array:', [
                'id' => $tiandyInFinal->manufacturer_id,
                'name' => $tiandyInFinal->name,
                'count' => $tiandyInFinal->products_count
            ]);
        }

        // Debug: Check what's being passed to the view
        $tiandyForView = $manufacturers->where('manufacturer_id', 269)->first();
        \Log::info('Manufacturers being passed to view:', [
            'count' => $manufacturers->count(),
            'tiandy_view' => $tiandyForView ? (array) $tiandyForView : null
        ]);

        // Debug: Log the main products query count
        \Log::info('Main products query count:', [
            'main_query_count' => $query->count(),
            'filterParam' => $filterParam,
            'categoryIds' => $categoryIds
        ]);

        // Get price range for filter for current category (with caching)
        $priceRangeCacheKey = 'price_range_category_' . $category->category_id;

        $priceRange = \Cache::remember($priceRangeCacheKey, 1800, function () use ($categoryIds) {
            return Product::published()
                ->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('vjprf_jshopping_products_to_categories.category_id', $categoryIds);
                })
                ->selectRaw('MIN(product_price) as min_price, MAX(product_price) as max_price')
                ->first();
        });

        // Load products with their characteristics (with caching and optimized locale fields)
        // Remove caching to debug the issue
        $products = $query->with(['productCharacteristics.extraField', 'productCharacteristics.extraFieldValue'])
                         ->paginate(request()->get('per_page', 20))->withQueryString();

        // Get child categories for the current category (with caching)
        $childCategoriesCacheKey = 'child_categories_' . $category->category_id;

        $childCategories = \Cache::remember($childCategoriesCacheKey, 3600, function () use ($category) {
            $childCategories = Category::active()
                ->where('category_parent_id', $category->category_id)
                ->ordered()
                ->withLocaleFields() // Use optimized locale fields
                ->get();

            // Load subcategories and complexes for each child category
            foreach ($childCategories as $childCategory) {
                // Get subcategories with optimized locale fields
                $subcategories = Category::where('category_parent_id', $childCategory->category_id)
                    ->where('category_publish', 1)
                    ->orderBy('ordering', 'asc')
                    ->withLocaleFields() // Use optimized locale fields
                    ->get();

                // Get complexes for this category with optimized locale fields
                $complexes = Complex::where('category_id', $childCategory->category_id)
                    ->where('complex_publish', 1)
                    ->orderBy('ordering', 'asc')
                    ->withLocaleFields() // Use optimized locale fields
                    ->get();

                $childCategory->subcategories = $subcategories;
                $childCategory->complexes = $complexes;
            }

            return $childCategories;
        });

        // Get root categories for navigation (with caching)
        $categoriesCacheKey = 'navigation_categories';

        $categories = \Cache::remember($categoriesCacheKey, 7200, function () {
            return Category::active()
                ->root()
                ->ordered()
                ->withCount('products')
                ->withLocaleFields() // Use optimized locale fields
                ->with(['subcategories' => function ($query) {
                    $query->active()->ordered()->withLocaleFields()->with(['parent', 'subcategories' => function ($subQuery) {
                        $subQuery->active()->ordered()->withLocaleFields();
                    }]);
                }])
                ->get();
        });

        // Render universal Vue page with Content component so that JshoppingCategory can handle rendering
        $menus = $this->buildMenus(['main-menu-add', 'mainmenu-rus']);
        $menuItemsTop = $menus['main-menu-add'] ?? [];
        $menuItemsMain = $menus['mainmenu-rus'] ?? [];

        $linkParams = [
            'option' => 'com_jshopping',
            'view' => 'category',
            'layout' => 'category',
            'task' => 'view',
            'category_id' => (string) $category->category_id,
        ];

        $pageData = [
            'language' => app()->getLocale(),
            'siteName' => config('app.name', 'Site'),
            'siteDescription' => '',
            'menuItem' => [
                'id' => null,
                'title' => $category->name,
                'alias' => $category->alias,
                'path' => $category->full_path,
            ],
            'linkParams' => $linkParams,
            'componentType' => 'Content',
            'additionalData' => [],
        ];

        $activeMenuId = null;
        $componentClass = 'default';

        return view('front.page', compact('pageData', 'menuItemsTop', 'menuItemsMain', 'activeMenuId', 'componentClass'));
    }

    /**
     * Get category and all its subcategory IDs recursively
     *
     * @param Category $category
     * @return array
     */
    private function getCategoryAndSubcategoryIds(Category $category): array
    {
        $ids = [$category->category_id];

        // Get all subcategories recursively
        $subcategories = Category::active()
            ->where('category_parent_id', $category->category_id)
            ->get();

        foreach ($subcategories as $subcategory) {
            $ids = array_merge($ids, $this->getCategoryAndSubcategoryIds($subcategory));
        }

        return $ids;
    }

    /**
     * Get products for AJAX requests (for dynamic loading).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getProducts(Request $request): JsonResponse
    {
        $query = Product::published();

        // Apply same filters as index method
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('price_min') || $request->filled('price_max')) {
            $minPrice = $request->price_min ? (float) $request->price_min : 0;
            $maxPrice = $request->price_max ? (float) $request->price_max : 999999;
            $query->priceRange($minPrice, $maxPrice);
        }

        // Manufacturer filtering
        if ($request->filled('manufacturers')) {
            $manufacturerIds = array_map('intval', explode(',', $request->manufacturers));
            $query->whereIn('product_manufacturer_id', $manufacturerIds);
        }

        if ($request->filled('special_price')) {
            $query->specialPrice();
        }

        // Category filter
        if ($request->filled('category')) {
            $category = Category::find($request->category);
            if ($category) {
                $categoryIds = $this->getCategoryAndSubcategoryIds($category);
                $query->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('vjprf_jshopping_products_to_categories.category_id', $categoryIds);
                });
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'date_added');
        $sortDirection = $request->get('direction', 'desc');

        switch ($sortBy) {
            case 'price':
                $query->orderBy('product_price', $sortDirection);
                break;
            case 'price_asc':
                $query->priceAsc();
                break;
            case 'price_desc':
                $query->priceDesc();
                break;
            case 'popularity':
                $query->popular();
                break;
            case 'rating':
                $query->byRating();
                break;
            case 'name':
                $query->orderBy('name_uk-UA', $sortDirection);
                break;
            default:
                $query->orderBy('product_date_added', 'desc');
                break;
        }

        $perPage = $request->get('per_page', 20);
        $products = $query->with(['manufacturer', 'categories', 'productCharacteristics.extraField', 'productCharacteristics.extraFieldValue'])
                         ->paginate($perPage);

        // Get manufacturers for filter
        $manufacturers = Manufacturer::published()
            ->ordered()
            ->withCount(['products' => function ($query) {
                $query->published();
            }])
            ->having('products_count', '>', 0)
            ->get();

        // Group products by category for Vue components
        $categories = [];
        $productsByCategory = $products->groupBy(function ($product) {
            return $product->categories->first() ? $product->categories->first()->category_id : 'uncategorized';
        });

        foreach ($productsByCategory as $categoryId => $categoryProducts) {
            if ($categoryId === 'uncategorized') {
                $categories[] = [
                    'category_id' => 'uncategorized',
                    'name' => 'Інші товари',
                    'description' => 'Товари без категорії',
                    'url' => null,
                    'products' => $this->formatProductsForVue($categoryProducts)
                ];
            } else {
                $category = Category::find($categoryId);
                if ($category) {
                    $categories[] = [
                        'category_id' => $category->category_id,
                        'name' => $category->name,
                        'description' => $category->description,
                        'url' => route('category.show', $category->full_path),
                        'products' => $this->formatProductsForVue($categoryProducts)
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'manufacturers' => $manufacturers,
            'total' => $products->total(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total()
            ]
        ]);
    }

    /**
     * Format products for Vue components
     *
     * @param \Illuminate\Database\Eloquent\Collection $products
     * @return array
     */
    private function formatProductsForVue($products)
    {
        return $products->map(function ($product) {
            return [
                'product_id' => $product->product_id,
                'name' => $product->name,
                'short_description' => $product->product_short_description,
                'thumbnail_url' => $product->thumbnail_url,
                'product_price' => $product->product_price,
                'formatted_price' => $product->formatted_price,
                'old_price' => $product->product_price > 2000 ? number_format($product->product_price * 1.2, 2) : null,
                'hits' => $product->hits ?? 0,
                'rating' => 4, // Default rating
                'reviews_count' => rand(10, 50),
                'url' => route('products.show-by-path', $product->full_path),
                'extra_fields' => $this->formatExtraFields($product),
                'manufacturer' => $product->manufacturer ? [
                    'name' => $product->manufacturer->name,
                    'logo' => $product->manufacturer->manufacturer_logo
                ] : null
            ];
        })->toArray();
    }

    /**
     * Format extra fields for Vue components
     *
     * @param \App\Models\Product\Product $product
     * @return array
     */
    private function formatExtraFields($product)
    {
        try {
            $extraFieldsData = $product->product_extra_fields ?? [];
            if (!is_array($extraFieldsData) || is_string($extraFieldsData)) {
                $extraFieldsData = [];
            }
            if (is_object($extraFieldsData) && method_exists($extraFieldsData, 'toArray')) {
                $extraFieldsData = $extraFieldsData->toArray();
            }
        } catch (\Exception $e) {
            $extraFieldsData = [];
        }

        $formattedFields = [];
        if (is_array($extraFieldsData) && !empty($extraFieldsData)) {
            foreach ($extraFieldsData as $extraField) {
                if (is_array($extraField) && isset($extraField['field_name']) && isset($extraField['field_value'])) {
                    $formattedFields[] = [
                        'field_name' => $extraField['field_name'],
                        'field_value' => $extraField['field_value']
                    ];
                }
            }
        }

        return $formattedFields;
    }

    /**
     * Get filter options for AJAX requests.
     *
     * @return JsonResponse
     */
    public function getFilters(): JsonResponse
    {
        $manufacturers = Manufacturer::published()
            ->ordered()
            ->get();

        $priceRange = Product::published()
            ->selectRaw('MIN(product_price) as min_price, MAX(product_price) as max_price')
            ->first();

        return response()->json([
            'manufacturers' => $manufacturers,
            'price_range' => $priceRange,
        ]);
    }

    /**
     * Search products with autocomplete.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'products' => [],
                'categories' => [],
                'manufacturers' => []
            ]);
        }

        // Search products
        $products = Product::published()
            ->search($query)
            ->withLocaleFields() // Use optimized locale fields
            ->with('categories')
            ->limit(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->name,
                    'price' => $product->formatted_price,
                    'image' => $product->thumbnail_url,
                    'url' => route('products.show-by-path', $product->full_path),
                    'ean' => $product->product_ean,
                    'manufacturer_code' => $product->manufacturer_code,
                ];
            });

        // Search categories
        $categories = Category::active()
            ->withLocaleFields() // Use optimized locale fields
            ->where(function ($q) use ($query) {
                $q->where('name_uk-UA', 'like', "%{$query}%")
                  ->orWhere('name_ru-UA', 'like', "%{$query}%")
                  ->orWhere('name_en-GB', 'like', "%{$query}%")
                  ->orWhere('alias_uk-UA', 'like', "%{$query}%")
                  ->orWhere('alias_ru-UA', 'like', "%{$query}%")
                  ->orWhere('alias_en-GB', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->category_id,
                    'name' => $category->name,
                    'url' => route('category.show', $category->full_path),
                ];
            });

        // Search manufacturers
        $manufacturers = Manufacturer::published()
            ->withLocaleFields() // Use optimized locale fields
            ->where(function ($q) use ($query) {
                $q->where('name_uk-UA', 'like', "%{$query}%")
                  ->orWhere('name_ru-UA', 'like', "%{$query}%")
                  ->orWhere('name_en-GB', 'like', "%{$query}%")
                  ->orWhere('code_1c', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($manufacturer) {
                return [
                    'id' => $manufacturer->manufacturer_id,
                    'name' => $manufacturer->name,
                    'url' => route('products.index', ['manufacturer' => $manufacturer->manufacturer_id]),
                ];
            });

        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'manufacturers' => $manufacturers
        ]);
    }

    /**
     * Display a listing of categories.
     */
    public function categories()
    {
        $categories = Category::where('category_publish', 1)
            ->orderBy('name')
            ->get();

        return view('front.categories.index', compact('categories'));
    }

    /**
     * SSR partial: render small products module HTML for hydration.
     */
    public function moduleHtml(Request $request)
    {
        $limit = (int) $request->get('limit', 3);
        $random = (bool) $request->get('random', false);

        // Reuse getProducts to fetch a list quickly
        $req = Request::create('/api/products', 'GET', [
            'per_page' => max(1, $limit),
        ]);
        /** @var JsonResponse $json */
        $json = $this->getProducts($req);
        $data = $json->getData(true);

        // Prefer products list from API; our API groups products under categories
        $list = [];
        if (isset($data['products']) && is_array($data['products'])) {
            $list = $data['products'];
        } elseif (isset($data['data']) && is_array($data['data'])) {
            $list = $data['data'];
        } elseif (!empty($data['categories']) && is_array($data['categories'])) {
            // Flatten categories[*].products
            foreach ($data['categories'] as $cat) {
                if (!empty($cat['products']) && is_array($cat['products'])) {
                    foreach ($cat['products'] as $p) {
                        $list[] = $p;
                    }
                }
            }
        }

        if ($random && !empty($list)) {
            shuffle($list);
        }
        $list = array_slice($list, 0, max(1, $limit));

        $products = array_map(function ($p) {
            $id = $p['product_id'] ?? $p['id'] ?? null;
            $name = $p['name'] ?? $p['title'] ?? '';
            $image = $p['thumbnail_url'] ?? $p['image'] ?? $p['img'] ?? '/images/placeholder.png';
            $slug = $p['full_path'] ?? $p['slug'] ?? null;
            if (!empty($p['url'])) {
                $url = $p['url'];
            } elseif ($slug) {
                $url = route('products.show-by-path', $slug);
            } elseif ($id && is_numeric($id)) {
                $url = route('products.show', (int) $id);
            } else {
                $url = '#';
            }
            return compact('id', 'name', 'image', 'url');
        }, $list);

        return view('share.products.module', compact('products'));
    }

}
