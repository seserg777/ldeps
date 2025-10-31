# Laravel Best Practices (Project-specific)

- SSR-first rendering: Render primary content and navigation in Blade. Use Alpine.js only for progressive enhancement.
- Lean controllers: Controllers orchestrate services; no heavy business logic inside controllers.
- DTOs/Resources: Shape API responses with Resources; validate inputs with Form Requests.
- Routes ordering: Place specific routes (e.g., `/products/html`) before parameterized routes (e.g., `/products/{id}`).
- Eloquent relationships: Eager-load relationships where needed to avoid N+1; paginate lists.
- Caching: Cache menu trees and common aggregates; invalidate on admin updates.
- Localization: Normalize locales (e.g., `ru-UA`/`uk-UA`) and match by base language when appropriate.
- Views: Keep Blade fragments small and composable under `resources/views/share`.
- Assets: Build via Vite; prefer CDN fallbacks for critical libraries if needed.
- Security: Include CSRF tokens for POST/PUT/DELETE; escape content unless intentionally rendering trusted HTML.
- Performance: Prevent CLS by reserving dimensions; use skeletons for async sections.
- Testing: Cover controllers/services with Feature/Unit tests; use factories and seeders.
- Naming: Use explicit route names and descriptive controller/view names.
- Error handling: Return meaningful HTTP status codes/messages; avoid swallowing exceptions.
- Logging: Log unexpected states with enough context (IDs, user, URL).
