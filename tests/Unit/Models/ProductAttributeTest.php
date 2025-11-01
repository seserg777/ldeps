<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ProductAttribute;

class ProductAttributeTest extends TestCase
{
    /** @test */
    public function it_has_correct_table_name()
    {
        $attribute = new ProductAttribute();
        
        $this->assertEquals('vjprf_jshopping_products_attr', $attribute->getTable());
    }

    /** @test */
    public function it_returns_attribute_values()
    {
        $attribute = new ProductAttribute();
        $attribute->attr_12 = 100;
        $attribute->attr_13 = 200;
        $attribute->attr_15 = 300;

        $values = $attribute->getAttributeValues();

        $this->assertIsArray($values);
        $this->assertArrayHasKey(12, $values);
        $this->assertArrayHasKey(13, $values);
        $this->assertArrayHasKey(15, $values);
        $this->assertEquals(100, $values[12]);
        $this->assertEquals(200, $values[13]);
        $this->assertEquals(300, $values[15]);
    }

    /** @test */
    public function it_filters_out_null_attributes()
    {
        $attribute = new ProductAttribute();
        $attribute->attr_12 = 100;
        $attribute->attr_13 = null;
        $attribute->attr_15 = 300;

        $values = $attribute->getAttributeValues();

        $this->assertArrayHasKey(12, $values);
        $this->assertArrayNotHasKey(13, $values);
        $this->assertArrayHasKey(15, $values);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $attribute = new ProductAttribute();
        
        $fillable = $attribute->getFillable();
        
        $this->assertContains('product_id', $fillable);
        $this->assertContains('price', $fillable);
        $this->assertContains('retail_price', $fillable);
        $this->assertContains('ean', $fillable);
    }
}

