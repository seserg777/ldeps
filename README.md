# LDEPS - Laboratory and Diagnostic Equipment, Parts and Supplies

Laravel + Alpine.js project for e‑commerce and content. The frontend uses PHP‑SSR (Blade) for initial HTML and Alpine for progressive enhancement. Menus and widgets are rendered server‑side for SEO; client JS adds small interactions only.

## Features

- **Admin Panel** with dark theme design and English interface
- **Product and Category Management** with hierarchical structure
- **Content Management System** with categories and articles
- **Dynamic Menu System** with module-based rendering via `vjprf_modules_menu`
- **User System** with access groups and authentication
- **Shopping Cart** and Wishlist with lightweight Alpine.js behaviors
- **Alpine.js Frontend** with Vite build system
- **PHP‑SSR** for menus and modules; Alpine for enhancement
- **Blade-first architecture** with SSR fragments and reusable components
- **JoomShopping integration**: root category landings and product grids for sub‑categories
- **Multilingual Support** (Ukrainian, Russian, English) with locale mapping
- **Modular Architecture** following Laravel best practices
- **Static Analysis** with PHPStan and pre-commit hooks
- **Responsive Design** with Tailwind CSS
- **SEO-friendly URLs** with middleware-based routing

## Project Structure (key paths)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Web/                  # Frontend controllers (PHP‑SSR)
│   │   │   ├── ProductController.php       # product pages + category pages (SSR Blade)
│   │   │   ├── JshoppingController.php     # JSON for JoomShopping categories/complexes
│   │   │   ├── CartController.php
│   │   │   ├── AuthController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── WishlistController.php
│   │   │   └── SaleBannerController.php
│   │   ├── Api/           # API controllers
│   │   │   └── ProductApiController.php
│   │   └── Admin/         # Admin panel controllers
│   │       ├── ProductAdminController.php
│   │       ├── CategoryAdminController.php
│   │       ├── ManufacturerAdminController.php
│   │       ├── OrderAdminController.php
│   │       ├── CustomerAdminController.php
│   │       ├── MenuTypeController.php
│   │       ├── MenuController.php
│   │       ├── CategoryController.php
│   │       └── ContentController.php
│   ├── Middleware/        # Custom middleware
│   │   ├── SetLocale.php             # Language detection
│   │   ├── ResolvePageController.php # Route content to correct controller
│   │   └── CustomAuthenticate.php    # Custom auth
│   ├── Requests/          # Form request validation
│   └── Resources/         # API resources
├── Models/
│   ├── Product/           # Product-related models
│   │   └── Product.php
│   ├── Category/          # Category-related models
│   │   └── Category.php
│   ├── User/              # User-related models
│   │   ├── User.php
│   │   ├── Usergroup.php
│   │   └── UserProfile.php
│   └── Order/             # Order-related models (future)
├── Services/
│   ├── Product/           # Product-related services
│   │   ├── ProductService.php
│   │   ├── SearchService.php
│   │   └── CacheService.php
│   ├── Category/          # Category-related services
│   │   └── CategoryService.php
│   └── Cart/              # Cart-related services (future)
├── Repositories/          # Data access layer
├── Jobs/                  # Background jobs
├── Events/                # Application events
├── Listeners/             # Event listeners
├── Helpers/               # Helper classes
│   ├── MenuRenderer.php          # Menu and module rendering
│   ├── ModuleHelper.php          # Module utilities
│   └── ProductHelper.php         # Product utilities
├── Traits/                # Reusable traits
│   └── HasLocalizedFields.php    # Multilingual field accessors
└── DTOs/                  # Data Transfer Objects

resources/
├── views/
│   ├── front/
│   │   ├── homepage.blade.php   # SSR homepage content
│   │   ├── page.blade.php       # SSR generic page content
│   │   └── products/
│   │       └── partials/
│   │           ├── product-detail.blade.php
│   │           └── modifications.blade.php
│   ├── components/
│   │   └── menu.blade.php       # Reusable menu component
│   ├── admin/
│   │   ├── components/
│   │   │   └── multilang-field.blade.php  # Multilingual input fields
│   │   └── products/
│   │       └── partials/
│   │           ├── attributes.blade.php   # Product variations table
│   │           └── characteristics.blade.php
│   └── share/
│       ├── menu/html.blade.php       # SSR menu HTML
│       └── layouts/
│           └── base.blade.php         # Base layout with itemid detection
├── js/
│   ├── app.js                    # Alpine init + cart actions
│   └── features/                 # (legacy removed)
└── css/

routes/
├── web.php               # Web routes
├── api.php               # API routes
└── console.php           # Console routes

database/
├── migrations/           # Database migrations
├── seeders/              # Database seeders
└── factories/            # Model factories

tests/
├── Feature/              # Feature tests
└── Unit/                 # Unit tests
```

## Installation

1. Clone the repository
2. Copy `.env.example` to `.env`
3. Configure database connection
4. Backend setup:
   ```bash
   composer install
   php artisan migrate
   php artisan db:seed
   ```
5. Frontend build (Vite):
   ```bash
   npm install
   npm run build    # production build
   # or npm run dev # development
   ```

> In production, Blade falls back to built assets if the Vite dev server is unavailable.

## Admin Panel

Access: `/admin`
- Login: users with admin privileges
- Completely isolated interface
- Universal Grid component for data management
- Dark theme design

## Architecture Benefits

- **Modularity**: Logical grouping by functionality
- **Scalability**: Easy to add new modules
- **Maintainability**: Clear separation of concerns
- **Testability**: Isolated components for testing
- **Laravel Best Practices**: Follows framework conventions

## Dynamic Menu System

### How it works:

1. **Homepage Detection** - `Menu::getHomeMenuId($language)` retrieves home menu ID based on current locale
   ```sql
   SELECT * FROM vjprf_menu WHERE home = '1' AND language = 'uk-UA'
   ```

2. **Module Loading** - `MenuRenderer::getModulesForPage($menuId, $includeGlobal)` loads modules for current page
   ```sql
   SELECT m.* FROM vjprf_modules m 
   INNER JOIN vjprf_modules_menu mm ON m.id = mm.moduleid 
   WHERE mm.menuid = ? AND m.published = 1
   ```

3. **Menu Filtering** - `MenuRenderer::getMenuModules($modules)` filters only menu modules (`mod_menu`)

4. **Rendering** - `MenuRenderer::renderMenuModules($menuModules)` renders all menus indexed by position/type

5. **Blade Component** - `<x-menu name="main-menu-add" :menus="$renderedMenus" />`

### Controller Pattern:

```php
$activeMenuId = MenuRenderer::detectActiveMenuId($entity);
$pageModules = MenuRenderer::getModulesForPage($activeMenuId, true);
$menuModules = MenuRenderer::getMenuModules($pageModules);
$renderedMenus = MenuRenderer::renderMenuModules($menuModules);

return view('front.page', compact('renderedMenus', 'activeMenuId', ...));
```

### Middleware Routing:

- `ResolvePageController` middleware intercepts `/{path}.html` requests
- Routes to appropriate controller based on component type (Content, Banners, etc.)
- Uses `$request->attributes` to pass data between middleware and controller

## Testing & Quality

- **Unit Tests**: Models and core logic (`tests/Unit/`)
- **PHPStan**: Static analysis at level 1 with pre-commit hook
- **GitHub Actions**: CI/CD pipeline runs tests and analysis
- Run tests: `php artisan test`
- Run static analysis: `composer analyse`

See `TESTING.md` and `STATIC_ANALYSIS.md` for details.

## Public Endpoints (SSR + JSON)

- `GET /` – Homepage with dynamic menu loading
- `GET /{path}.html` – SEO-friendly pages (routed via middleware)
- `GET /menu/html/{menutype}?maxLevels=...&language=...` – SSR menu HTML
- `GET /products/html?limit=3&random=1` – SSR product module HTML
- `GET /api/jshopping/category/{id}` – JSON for JoomShopping categories
- `GET /api/products` – product listing API (supports filtering)

## Frontend Pages

- **Homepage**: `resources/views/front/homepage.blade.php` — SSR with dynamic menus via `<x-menu>`
- **Inner pages**: `resources/views/front/page.blade.php` — SSR for products, content, banners
- **Menu component**: `resources/views/components/menu.blade.php` — Reusable menu renderer
