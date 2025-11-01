# Static Analysis & Code Quality

This document describes tools and practices for automatic error detection in the LDEPS application.

## Why Tests Missed the Error

The `Undefined variable $siteName` error wasn't caught because:

1. **No Feature Tests** - We removed all HTTP/integration tests that would actually call controllers
2. **Unit Tests Only** - Current tests only check model logic in isolation
3. **No View Data Validation** - Tests don't verify that controllers pass required variables to views

## Tools for Automatic Error Detection

### 1. PHPStan (Static Analysis)

**What it catches:**
- Undefined variables
- Type mismatches
- Missing return types
- Unreachable code
- Method calls on null

**Installation:**
```bash
composer require --dev nunomaduro/larastan
```

**Run analysis:**
```bash
vendor/bin/phpstan analyse
```

**Configuration:** See `phpstan.neon`

**Example output for our bug:**
```
------ -------------------------------------------------------
 Line   HomeController.php
------ -------------------------------------------------------
 31     Variable $siteName might not be defined.
 31     Variable $siteDescription might not be defined.
 31     Variable $language might not be defined.
------ -------------------------------------------------------
```

### 2. Psalm (Alternative Static Analyzer)

**Installation:**
```bash
composer require --dev vimeo/psalm
vendor/bin/psalm --init
```

**Run:**
```bash
vendor/bin/psalm
```

### 3. Laravel Dusk (Browser Tests)

**What it catches:**
- Runtime errors on actual pages
- Missing view variables
- JavaScript errors
- UI/UX issues

**Installation:**
```bash
composer require --dev laravel/dusk
php artisan dusk:install
```

**Example test:**
```php
public function test_homepage_loads_without_errors()
{
    $this->browse(function (Browser $browser) {
        $browser->visit('/')
                ->assertSee('LDEPS')
                ->assertDontSee('ErrorException')
                ->assertDontSee('Undefined variable');
    });
}
```

### 4. Feature Tests with View Data Assertions

**Example:**
```php
public function test_homepage_returns_required_variables()
{
    $response = $this->get('/');
    
    $response->assertStatus(200);
    $response->assertViewHas('siteName');
    $response->assertViewHas('siteDescription');
    $response->assertViewHas('language');
    $response->assertViewHas('menuTopHtml');
    $response->assertViewHas('menuMainHtml');
}
```

### 5. IDE Plugins

**PhpStorm:**
- Laravel Idea plugin
- PHP Inspections (EA Extended)

**VS Code:**
- PHP Intelephense
- Laravel Extension Pack
- Laravel goto view

### 6. Pre-commit Hooks

**Install:**
```bash
composer require --dev brainmaestro/composer-git-hooks
```

**Configure in composer.json:**
```json
{
    "extra": {
        "hooks": {
            "pre-commit": [
                "vendor/bin/phpstan analyse",
                "vendor/bin/phpunit --testsuite=Unit"
            ]
        }
    }
}
```

**Activate:**
```bash
vendor/bin/cghooks add --ignore-lock
```

### 7. Continuous Integration (GitHub Actions)

See `.github/workflows/tests.yml` for automated testing on every push/PR.

**What it does:**
- Runs PHPStan on every commit
- Executes all tests
- Reports coverage
- Blocks merge if tests fail

## Recommended Workflow

### For Development:
1. **Run PHPStan before committing:**
   ```bash
   vendor/bin/phpstan analyse
   ```

2. **Run tests locally:**
   ```bash
   php artisan test
   ```

3. **Use IDE with static analysis enabled**

### For CI/CD:
1. **Pre-commit hook** - Fast checks (PHPStan + Unit tests)
2. **GitHub Actions** - Full suite (Static analysis + All tests)
3. **Code review** - Manual inspection

## Best Practices

### 1. Type Hinting Everywhere
```php
// Bad
public function index()
{
    $data = $this->getData();
    return view('home', $data);
}

// Good
public function index(): View
{
    $data = $this->getData();
    assert(is_array($data));
    return view('home', $data);
}
```

### 2. Explicit Variable Declaration
```php
// Bad
return view('page', compact('var1', 'var2', 'var3'));

// Good
return view('page', [
    'var1' => $var1,
    'var2' => $var2,
    'var3' => $var3,
]);
```

### 3. View Composers for Shared Data
```php
// In AppServiceProvider
View::composer('*', function ($view) {
    $view->with('siteName', config('app.name'));
    $view->with('language', app()->getLocale());
});
```

### 4. Request Objects with Validation
```php
public function store(StoreProductRequest $request): RedirectResponse
{
    // $request->validated() is guaranteed to have all required fields
    Product::create($request->validated());
}
```

## Quick Setup

```bash
# Install static analysis
composer require --dev nunomaduro/larastan

# Install pre-commit hooks
composer require --dev brainmaestro/composer-git-hooks

# Configure hooks in composer.json, then:
vendor/bin/cghooks add --ignore-lock

# Run analysis
vendor/bin/phpstan analyse
```

## Summary

To catch errors like undefined variables automatically:

1. ✅ **PHPStan/Psalm** - Catches most PHP errors before runtime
2. ✅ **Feature Tests** - Verifies actual HTTP responses and view data
3. ✅ **Pre-commit Hooks** - Prevents committing broken code
4. ✅ **CI/CD Pipeline** - Automated quality gates
5. ✅ **IDE Integration** - Real-time feedback while coding

**Priority order:**
1. PHPStan (easiest, highest value)
2. Feature tests (medium effort, high value)
3. Pre-commit hooks (low effort, medium value)
4. CI/CD (medium effort, high value for teams)

