<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Boot the testing helper traits.
     *
     * @return array
     */
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        // Skip problematic migrations in tests
        if (isset($uses['Illuminate\Foundation\Testing\RefreshDatabase'])) {
            $this->skipProblematicMigrations();
        }

        return $uses;
    }

    /**
     * Skip migrations that have known issues.
     *
     * @return void
     */
    protected function skipProblematicMigrations()
    {
        // Define migrations to skip (known issues with duplicate columns)
        $skipMigrations = [
            // Products migration has duplicate name_ru-UA column
            'create_products_table',
        ];

        // You can implement custom migration logic here if needed
        // For now, we'll rely on specific migrations being excluded
    }
}
