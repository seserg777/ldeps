<?php

namespace Tests\Unit;

use App\Models\Product\Product;
use App\Models\Manufacturer;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
    }

    public function test_can_get_manufacturers_for_filter(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        Product::factory()->create([
            'product_manufacturer_id' => $manufacturer->manufacturer_id,
            'product_publish' => 1,
        ]);

        $manufacturers = $this->productService->getManufacturersForFilter();

        $this->assertCount(1, $manufacturers);
        $this->assertEquals(1, $manufacturers->first()->products_count);
    }

    public function test_can_search_products(): void
    {
        Product::factory()->create([
            'name_uk-UA' => 'Test Product',
            'product_publish' => 1,
        ]);

        $products = $this->productService->searchProducts('Test', 10);

        $this->assertCount(1, $products);
        $this->assertEquals('Test Product', $products->first()->name);
    }

    public function test_can_get_price_range(): void
    {
        Product::factory()->create([
            'product_price' => 100,
            'product_publish' => 1,
        ]);
        Product::factory()->create([
            'product_price' => 1000,
            'product_publish' => 1,
        ]);

        $query = Product::published();
        $priceRange = $this->productService->getPriceRange($query);

        $this->assertEquals(100, $priceRange['min']);
        $this->assertEquals(1000, $priceRange['max']);
    }

    public function test_can_filter_products_by_price(): void
    {
        Product::factory()->create([
            'product_price' => 100,
            'product_publish' => 1,
        ]);
        Product::factory()->create([
            'product_price' => 1000,
            'product_publish' => 1,
        ]);

        $filters = [
            'price_min' => 200,
            'price_max' => 800,
        ];

        $query = $this->productService->getFilteredProducts($filters);
        $products = $query->get();

        $this->assertCount(0, $products);
    }

    public function test_can_filter_products_by_manufacturer(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        Product::factory()->create([
            'product_manufacturer_id' => $manufacturer->manufacturer_id,
            'product_publish' => 1,
        ]);
        Product::factory()->create([
            'product_publish' => 1,
        ]);

        $filters = [
            'manufacturer' => [$manufacturer->manufacturer_id],
        ];

        $query = $this->productService->getFilteredProducts($filters);
        $products = $query->get();

        $this->assertCount(1, $products);
    }
}
