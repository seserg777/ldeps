<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Module;

class ModuleTest extends TestCase
{
    /** @test */
    public function it_has_correct_table_name()
    {
        $module = new Module();
        
        $this->assertEquals('vjprf_modules', $module->getTable());
    }

    /** @test */
    public function it_has_correct_primary_key()
    {
        $module = new Module();
        
        $this->assertEquals('id', $module->getKeyName());
    }

    /** @test */
    public function it_returns_params_as_array_from_json()
    {
        $module = new Module();
        $module->params = json_encode(['key' => 'value', 'number' => 123]);

        $params = $module->params_array;

        $this->assertIsArray($params);
        $this->assertEquals('value', $params['key']);
        $this->assertEquals(123, $params['number']);
    }

    /** @test */
    public function it_returns_empty_array_for_null_params()
    {
        $module = new Module();
        $module->params = null;

        $params = $module->params_array;

        $this->assertIsArray($params);
        $this->assertEmpty($params);
    }

    /** @test */
    public function it_has_fillable_attributes()
    {
        $module = new Module();
        
        $fillable = $module->getFillable();
        
        $this->assertContains('title', $fillable);
        $this->assertContains('position', $fillable);
        $this->assertContains('published', $fillable);
        $this->assertContains('module', $fillable);
        $this->assertContains('params', $fillable);
    }
}

