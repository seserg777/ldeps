<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_products_list(): void
    {
        Product::factory()->count(5)->published()->create();

        $response = $this->getJson('/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'image',
                        'url',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);
    }

    public function test_can_search_products(): void
    {
        Product::factory()->create([
            'name_uk-UA' => 'Test Product',
            'product_publish' => 1,
        ]);

        $response = $this->getJson('/api/v1/products/search?q=Test');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'products',
                'categories',
                'manufacturers',
            ]);
    }

    public function test_can_get_product_by_path(): void
    {
        $product = Product::factory()->published()->create([
            'alias_uk-UA' => 'test-product',
        ]);

        $response = $this->getJson('/api/v1/products/test-product');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'price',
                    'image',
                    'url',
                ]
            ]);
    }

    public function test_returns_404_for_nonexistent_product(): void
    {
        $response = $this->getJson('/api/v1/products/nonexistent-product');

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Product not found'
            ]);
    }

    public function test_can_filter_products_by_manufacturer(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        Product::factory()->create([
            'product_manufacturer_id' => $manufacturer->manufacturer_id,
            'product_publish' => 1,
        ]);

        $response = $this->getJson('/api/v1/products?manufacturer[]=' . $manufacturer->manufacturer_id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_can_filter_products_by_price_range(): void
    {
        Product::factory()->create([
            'product_price' => 100,
            'product_publish' => 1,
        ]);
        Product::factory()->create([
            'product_price' => 1000,
            'product_publish' => 1,
        ]);

        $response = $this->getJson('/api/v1/products?price_min=200&price_max=800');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
