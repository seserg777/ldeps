# ldeps

Laravel + Alpine.js project for e‑commerce and content. The frontend uses PHP‑SSR (Blade) for initial HTML and Alpine for progressive enhancement. Menus and widgets are rendered server‑side for SEO; client JS adds small interactions only.

## Features

- **Admin Panel** with dark theme design and English interface
- **Product and Category Management** with hierarchical structure
- **Content Management System** with categories and articles
- **Menu Management** with types and hierarchical items
- **User System** with access groups and authentication
- **Shopping Cart** and Wishlist with lightweight Alpine.js behaviors
- **Alpine.js Frontend** with Vite build system
- **PHP‑SSR** for menus and modules; Alpine for enhancement
- **Blade-first architecture** with SSR fragments
- **JoomShopping integration**: root category landings and product grids for sub‑categories
- **Multilingual Support** (Russian, Ukrainian, English)
- **Modular Architecture** following Laravel best practices
- **Responsive Design** with Tailwind CSS

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
├── Traits/                # Reusable traits
└── DTOs/                  # Data Transfer Objects

resources/
├── views/
│   ├── front/
│   │   ├── homepage.blade.php   # SSR homepage content
│   │   └── page.blade.php       # SSR generic page content
│   └── share/
│       ├── menu/html.blade.php       # SSR menu HTML
│       └── products/module.blade.php # SSR product module HTML
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

## Public Endpoints (SSR + JSON)

- `GET /menu/html/{menutype}?maxLevels=...&language=...` – SSR menu HTML used directly in Blade
- `GET /products/html?limit=3&random=1` – SSR product module HTML (вставляется через Blade partial)
- `GET /api/jshopping/category/{id}` – JSON for JoomShopping categories (children, complexes)
- `GET /api/products` – product listing API (supports `category`, `per_page`, filters)

## Frontend Pages

- Главная: `resources/views/front/homepage.blade.php` — SSR‑контент + подключаемые Blade partials (`search`, `products_module`, и т.д.).
- Внутренние страницы: `resources/views/front/page.blade.php` — SSR‑контент в зависимости от маршрута, меню вставляется как SSR HTML.
