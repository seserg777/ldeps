# Blade Components Documentation

## User Authentication Components

### Base Modal Component

**Component:** `<x-modal>`

Базовый компонент модального окна с поддержкой Alpine.js анимаций.

#### Props:
- `name` (required) - уникальное имя модального окна
- `title` (optional) - заголовок модального окна
- `maxWidth` (optional) - максимальная ширина: 'sm', 'md', 'lg', 'xl', '2xl' (default: '2xl')

#### Usage:

```blade
<x-modal name="my-modal" title="Modal Title" max-width="md">
    <p>Modal content here</p>
</x-modal>
```

#### Opening/Closing:

```javascript
// Open modal
$dispatch('open-modal', 'my-modal')

// Close modal
$dispatch('close-modal', 'my-modal')
```

---

### Auth Modal Component

**Component:** `<x-auth-modal>`

Модальное окно авторизации, расширяющее базовый компонент модального окна.

#### Features:
- Login form with username/email and password fields
- Password visibility toggle
- Remember me checkbox
- Forgot password link
- Registration link
- Stays on current page after authentication (redirect support)

#### Usage:

```blade
<x-auth-modal />

<!-- Open auth modal -->
<button @click="$dispatch('open-modal', 'auth-modal')">Login</button>
```

#### Form Fields:
- `username` - username or email
- `password` - user password
- `remember` - remember me checkbox
- `redirect` (hidden) - URL to redirect after login (current page by default)

---

### Profile Modal Component

**Component:** `<x-profile-modal>`

Модальное окно профиля для авторизованных пользователей.

#### Props:
- `user` (required) - authenticated user object

#### Features:
- Display user info (name, email)
- Edit profile link
- My orders link
- Logout button

#### Usage:

```blade
<x-profile-modal :user="auth()->user()" />

<!-- Open profile modal -->
<button @click="$dispatch('open-modal', 'profile-modal')">Profile</button>
```

---

### User Menu Component

**Component:** `<x-user-menu>`

Компонент меню пользователя с динамическими иконками и модальными окнами.

#### Features:
- **Unauthorized users**: User icon with lock → opens auth modal
- **Authorized users**: User icon without lock → opens profile modal
- Automatic modal inclusion based on auth state
- Hover effects and transitions

#### Usage:

```blade
<x-user-menu class="ml-4" />
```

#### Icons:
- **Unauthorized**: User silhouette with padlock overlay
- **Authorized**: User silhouette without lock

---

## Menu Component

**Component:** `<x-menu>`

Компонент для динамического отображения меню на основе модулей.

#### Props:
- `name` (required) - ключ меню (menutype, position или normalized title)
- `menus` (required) - массив рендеренных меню HTML
- `class` (optional) - дополнительные CSS классы

#### Usage:

```blade
<x-menu name="mainmenu-rus" :menus="$renderedMenus ?? []" class="menu-main" />
```

---

## Controller Pattern for User Menu

```php
// In any controller
use App\Helpers\MenuRenderer;

public function index()
{
    $activeMenuId = MenuRenderer::detectActiveMenuId();
    $pageModules = MenuRenderer::getModulesForPage($activeMenuId, true);
    $menuModules = MenuRenderer::getMenuModules($pageModules);
    $renderedMenus = MenuRenderer::renderMenuModules($menuModules);

    return view('front.page', compact('renderedMenus', 'activeMenuId'));
}
```

---

## Routes

### Authentication Routes:

- `GET /auth/login` - show login form
- `POST /auth/login` - process login
- `POST /auth/logout` - logout user
- `GET /auth/register` - show registration form (route name: `register`)
- `POST /auth/register` - process registration
- `GET /auth/password/request` - show password reset request form (route name: `password.request`)
- `POST /auth/password/email` - send password reset email
- `GET /auth/password/reset/{token}` - show password reset form
- `POST /auth/password/reset` - process password reset

### Profile Routes:

- `GET /profile` - user profile page (route name: `profile`)
- `GET /profile/editaccount` - edit profile form
- `PUT /profile` - update profile
- `GET /profile/orders` - user orders (route name: `profile.orders`)

---

## CSS Styles

Located in `resources/css/app.css`:

```css
/* Header actions container */
.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* User menu hover effects */
.user-menu button:hover {
    transform: scale(1.1);
}

/* Modal backdrop blur */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
```

---

## Alpine.js Events

### Modal Events:

```javascript
// Open any modal
$dispatch('open-modal', 'modal-name')

// Close any modal
$dispatch('close-modal', 'modal-name')

// Close on backdrop click (built-in)
// Close on ESC key (built-in)
```

### Examples:

```blade
<!-- Button to open auth modal -->
<button @click="$dispatch('open-modal', 'auth-modal')">
    Login
</button>

<!-- Button to open profile modal -->
<button @click="$dispatch('open-modal', 'profile-modal')">
    My Profile
</button>
```

---

## Complete Integration Example

```blade
{{-- In header section --}}
<header>
    <div class="container">
        <div class="row">
            <div class="col-2">
                <a href="/" class="logo">
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo">
                </a>
            </div>
            
            <div class="col-7">
                <x-menu name="mainmenu-rus" :menus="$renderedMenus ?? []" class="menu-main" />
            </div>

            <div class="col-3">
                <div class="header-actions">
                    @include('share.layouts.partials.search')
                    <x-user-menu class="ml-4" />
                </div>
            </div>
        </div>
    </div>
</header>
```

---

## Technical Notes

1. **Authentication Guard**: Uses custom guard (`Auth::guard('custom')`)
2. **Password Hashing**: Uses MD5 driver for legacy compatibility
3. **Session Management**: Automatically regenerates session on login/logout
4. **Redirect Behavior**: Stays on current page after authentication (via `redirect` field)
5. **Modal Animations**: Uses Alpine.js transitions with tailwind classes
6. **Icon SVGs**: Inline SVG for better control and performance

