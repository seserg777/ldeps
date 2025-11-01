# Testing Documentation

This document provides an overview of the testing strategy and how to run tests for the LDEPS application.

## Test Structure

The application uses PHPUnit for testing and follows Laravel's testing conventions:

### Test Types

1. **Unit Tests** (`tests/Unit/`)
   - Test individual classes and methods in isolation
   - Fast execution
   - No database dependencies

### Test Coverage

#### Unit Tests

**Models:**
- `ModuleTest` - Tests module model configuration
  - Table name configuration
  - Primary key configuration
  - Params JSON parsing
  - Fillable attributes
  
- `ProductAttributeTest` - Tests product attribute model
  - Table name configuration
  - Attribute value extraction
  - Null value filtering
  - Fillable attributes

**Note**: This is a basic test suite covering model configuration and data accessors. Feature tests requiring database migrations were intentionally excluded due to existing database schema complexities. Future enhancements should include:
- Integration tests with test database
- API endpoint tests
- Frontend controller tests
- Admin panel CRUD tests

## Running Tests

### Run All Tests

```bash
cd deps
php artisan test
```

or

```bash
vendor/bin/phpunit
```

### Run Specific Test Suites

**Unit tests only:**
```bash
php artisan test --testsuite=Unit
```

**Feature tests only:**
```bash
php artisan test --testsuite=Feature
```

### Run Specific Test Files

```bash
php artisan test tests/Unit/Helpers/MenuRendererTest.php
```

### Run Specific Test Methods

```bash
php artisan test --filter it_detects_active_menu_id_from_product
```

### Run with Coverage

```bash
php artisan test --coverage
```

To generate HTML coverage report:
```bash
vendor/bin/phpunit --coverage-html build/coverage
```

## Test Database

Tests use SQLite in-memory database by default (configured in `phpunit.xml`):

```xml
<env name="DB_DATABASE" value=":memory:"/>
<env name="DB_CONNECTION" value="sqlite"/>
```

This ensures:
- Fast test execution
- No interference with development database
- Clean state for each test run

## Writing Tests

### Test Naming Conventions

- Test methods should start with `test_` or use `@test` annotation
- Use descriptive names: `it_displays_product_page`, `it_creates_new_module`
- Follow the pattern: `it_[performs_action]_[expected_result]`

### Example Unit Test

```php
/** @test */
public function it_returns_params_as_array()
{
    $module = Module::factory()->create([
        'params' => json_encode(['key' => 'value'])
    ]);

    $params = $module->params_array;

    $this->assertIsArray($params);
    $this->assertEquals('value', $params['key']);
}
```

### Example Feature Test

```php
/** @test */
public function it_displays_product_page()
{
    $product = Product::factory()->create([
        'product_publish' => 1,
        'name_uk-UA' => 'Test Product'
    ]);

    $response = $this->get(route('products.show', $product->product_id));

    $response->assertStatus(200);
    $response->assertSee('Test Product');
}
```

## Factories

The application includes factories for easy test data generation:

- `ModuleFactory` - Create test modules
- `ProductFactory` - Create test products with states (published, onSale, outOfStock)
- `ProductAttributeFactory` - Create product attributes/modifications
- `MenuFactory` - Create menu items with states (published, component)
- `CategoryFactory` - Create categories with states (published, withParent)
- `ManufacturerFactory` - Create manufacturers

### Using Factories

```php
// Create a single instance
$module = Module::factory()->create();

// Create multiple instances
$products = Product::factory()->count(5)->create();

// Use states
$publishedProduct = Product::factory()->published()->create();
$saleProduct = Product::factory()->onSale()->create();

// Override attributes
$module = Module::factory()->create([
    'position' => 'header',
    'published' => 1
]);
```

## Best Practices

1. **Arrange-Act-Assert Pattern**
   - Arrange: Set up test data
   - Act: Perform the action
   - Assert: Verify the result

2. **Test Isolation**
   - Each test should be independent
   - Use `RefreshDatabase` trait for database tests
   - Don't rely on test execution order

3. **Descriptive Assertions**
   - Use specific assertions (`assertJsonStructure`, `assertDatabaseHas`)
   - Add assertion messages when helpful

4. **Mock External Dependencies**
   - Mock APIs, file systems, external services
   - Keep unit tests fast and isolated

5. **Test Edge Cases**
   - Empty results
   - Null values
   - Invalid input
   - Unauthorized access

## Continuous Integration

Tests should be run automatically on:
- Every commit (pre-commit hook)
- Pull requests
- Before deployment

## Coverage Goals

- **Target**: 80%+ code coverage
- **Priority Areas**:
  - Business logic (Helpers, Services)
  - Controllers (Admin and Frontend)
  - Models (relationships, scopes, accessors)
  - API endpoints

## Troubleshooting

### Common Issues

**Tests fail with "Database not found":**
- Ensure SQLite is installed: `php -m | grep sqlite`
- Check `phpunit.xml` database configuration

**Factory not found:**
- Ensure factory class exists in `database/factories/`
- Check namespace matches directory structure
- Run `composer dump-autoload`

**Memory errors:**
- Increase PHP memory limit: `php -d memory_limit=512M artisan test`

**Slow tests:**
- Use `RefreshDatabase` instead of `DatabaseMigrations`
- Reduce factory relationships depth
- Use in-memory SQLite database

