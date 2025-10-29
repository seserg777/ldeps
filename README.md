# ldeps

Laravel project with comprehensive admin panel for e-commerce and content management.

## Features

- **Admin Panel** with dark theme design and English interface
- **Product and Category Management** with hierarchical structure
- **Content Management System** with categories and articles
- **Menu Management** with types and hierarchical items
- **User System** with access groups and authentication
- **Shopping Cart** and Wishlist with Vue.js components
- **Vue.js Frontend** with Composition API and Vite build system
- **Multilingual Support** (Russian, Ukrainian, English)
- **Modular Architecture** following Laravel best practices
- **Responsive Design** with Tailwind CSS

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Web/           # Frontend controllers
│   │   │   ├── ProductController.php
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
│   ├── admin/             # Admin panel views
│   ├── front/             # Frontend views
│   ├── share/             # Shared components
│   │   ├── layouts/       # Layout templates
│   │   └── components/    # Reusable components
│   └── components/        # Blade components
├── js/                    # JavaScript assets
└── css/                   # CSS assets

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
4. Run:
   ```bash
   composer install
   php artisan migrate
   php artisan db:seed
   ```

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
