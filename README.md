# ldeps

Laravel project with admin panel for product catalog management.

## Features

- **Admin Panel** with dark theme design
- **Product and Category Management**
- **User System** with access groups
- **API** for frontend
- **Shopping Cart** and Wishlist
- **Multilingual Support**

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
