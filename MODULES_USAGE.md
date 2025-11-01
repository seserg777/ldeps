# Module System Usage

## Overview

The module system allows you to display dynamic content in various positions across your website. Modules can be assigned to specific menu items and support different types with custom logic.

## Available Module Types

### 1. **Custom HTML Module** (`custom` or empty)
Display custom HTML content.

**Parameters:** None (content stored in `content` field)

**Example:**
```html
<div class="promo-banner">
  <h2>Special Offer!</h2>
  <p>Get 20% off on all products</p>
</div>
```

### 2. **Menu Module** (`mod_menu`)
Display a navigation menu.

**Parameters:**
- `menutype` - Menu type to display (e.g., "mainmenu", "footer-menu")

**Example params (JSON):**
```json
{
  "menutype": "mainmenu"
}
```

### 3. **Products Module** (`mod_products`)
Display a list of products.

**Parameters:**
- `limit` - Number of products to show (default: 6)
- `type` - Display type: `latest`, `random`, or `popular`
- `category_id` - Filter by category ID (optional)

**Example params (JSON):**
```json
{
  "limit": 8,
  "type": "random",
  "category_id": 5
}
```

### 4. **Articles Module** (`mod_articles`)
Display a list of articles/content.

**Parameters:**
- `limit` - Number of articles to show (default: 5)
- `category_id` - Filter by category ID (optional)
- `featured` - Show only featured articles (true/false)

**Example params (JSON):**
```json
{
  "limit": 5,
  "category_id": 3,
  "featured": true
}
```

### 5. **Search Module** (`mod_search`)
Display a search form with autocomplete.

**Parameters:**
- `placeholder` - Search input placeholder text
- `show_button` - Show/hide search button (default: true)

**Example params (JSON):**
```json
{
  "placeholder": "Search products...",
  "show_button": true
}
```

## Available Positions

- `top` - Top of the page
- `header` - Header area
- `content-top` - Above main content
- `content-bottom` - Below main content
- `footer` - Footer area
- `bottom` - Bottom of the page
- `left` - Left sidebar
- `right` - Right sidebar
- `breadcrumbs` - Breadcrumbs area
- `debug` - Debug position

## Usage in Templates

### Method 1: Using the partial (Recommended)

```blade
@include('share.layouts.partials.modules_position', ['position' => 'header'])
```

### Method 2: Using component directly

```blade
<x-module-renderer position="header" :active-menu-id="$activeMenuId ?? null" />
```

### Method 3: With specific active menu ID

```blade
@include('share.layouts.partials.modules_position', [
    'position' => 'content-top',
    'activeMenuId' => 123
])
```

## Creating Modules in Admin Panel

1. Navigate to **Admin → Modules**
2. Click **Create Module**
3. Fill in the form:
   - **Title**: Module name
   - **Position**: Where to display the module
   - **Module Type**: Choose from available types
   - **Published**: Enable/disable the module
   - **Show Title**: Display module title or not
   - **Content**: HTML content (for custom modules)
   - **Parameters**: JSON configuration for module behavior
   - **Assign to Menu Items**: Select which pages should display this module

4. Click **Create Module**

## Menu Assignment Logic

- If **no menu items** are assigned → module displays on **all pages**
- If **menu items are assigned** → module displays only on those **specific pages**
- Menu assignment is based on the active menu item ID

## Creating Custom Module Types

To add a new module type:

1. Create a new Blade view in `resources/views/components/modules/`
   - File name: `mod_yourtype.blade.php`

2. Access module data:
   - `$module->title` - Module title
   - `$module->content` - Content field
   - `$module->params_array` - Parsed JSON parameters

3. Example custom module (`mod_banner.blade.php`):

```blade
@php
    $params = $module->params_array;
    $imageUrl = $params['image_url'] ?? '';
    $linkUrl = $params['link_url'] ?? '#';
@endphp

<div class="banner-module">
    <a href="{{ $linkUrl }}">
        <img src="{{ $imageUrl }}" alt="{{ $module->title }}">
    </a>
</div>
```

## Styling Modules

Module styles are located in `resources/scss/components/_modules.scss`

Each module has CSS classes:
- `.module` - Base module class
- `.module-{id}` - Specific module ID
- `.module-type-{type}` - Module type
- `.module-position-{position}` - Module position

Example:
```scss
.module-type-mod_banner {
  margin: 2rem 0;
  
  img {
    width: 100%;
    height: auto;
    border-radius: 8px;
  }
}
```

## Performance Considerations

- Modules are loaded server-side (no AJAX by default)
- Use caching for module queries when appropriate
- Limit the number of modules per position for better performance
- Use `ordering` field to control display order

## Examples

### Display search in header
1. Create module with type `mod_search`
2. Set position to `header`
3. Don't assign to any menu items (show everywhere)

### Show featured products only on homepage
1. Create module with type `mod_products`
2. Set position to `content-top`
3. Params: `{"limit": 6, "type": "random"}`
4. Assign to homepage menu item only

### Custom promo banner on specific category
1. Create module with type `custom`
2. Add HTML content with banner code
3. Set position to `content-top`
4. Assign only to specific category menu item

