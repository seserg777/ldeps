# Layout Structure Documentation

## Overview
Modular layout structure for better management and component reusability.

## Layout Hierarchy

### 1. Base Layout (`base.blade.php`)
Base layout with common elements:
- HTML structure
- Meta tags
- CSS/JS includes
- Header and Footer
- Vue components

### 2. Specialized Layouts

#### App Layout (`app.blade.php`)
Standard layout for regular pages:
- Container with padding
- Standard content structure

#### Shop Layout (`shop.blade.php`)
Layout for product catalog:
- Sidebar support
- Responsive grid
- Filters and sorting

#### Auth Layout (`auth.blade.php`)
Layout for authentication pages:
- Centered form
- Minimalist design

#### Landing Layout (`landing.blade.php`)
Layout for landing pages:
- Full width
- Unrestricted sections

## Components

### Header (`partials/header.blade.php`)
- Navigation menu
- Search
- Shopping cart
- User menu

### Footer (`partials/footer.blade.php`)
- Company information
- Links
- Contact details
- Social networks

### Breadcrumbs (`components/breadcrumbs.blade.php`)
```php
<x-breadcrumbs :items="[
    ['title' => 'Catalog', 'url' => route('products.index')],
    ['title' => 'Product', 'url' => '#', 'icon' => 'fas fa-box']
]" />
```

### Sidebar (`components/sidebar.blade.php`)
```php
<x-sidebar title="Filters">
    <!-- Sidebar content -->
</x-sidebar>
```

### Page Header (`components/page-header.blade.php`)
```php
<x-page-header 
    title="Page Title" 
    subtitle="Page subtitle"
    icon="fas fa-icon"
>
    <x-slot name="actions">
        <!-- Action buttons -->
    </x-slot>
</x-page-header>
```

## Usage Examples

### Product Catalog Page
```php
@extends('share.layouts.shop')

@section('sidebar')
    <x-sidebar title="Filters">
        <!-- Filters -->
    </x-sidebar>
@endsection

@section('page-content')
    <!-- Products grid -->
@endsection
```

### Auth Page
```php
@extends('share.layouts.auth')

@section('page-content')
    <!-- Login form -->
@endsection
```

### Landing Page
```php
@extends('share.layouts.landing')

@section('page-content')
    <!-- Hero section -->
    <!-- Features -->
    <!-- CTA -->
@endsection
```

## Benefits

1. **Modularity** - Each component handles its own area
2. **Reusability** - Components can be reused
3. **Maintainability** - Easy to modify individual parts
4. **Consistency** - Uniform design
5. **Flexibility** - Different layouts for different page types
