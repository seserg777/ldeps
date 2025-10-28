<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
        :root {
            --sidebar-bg: #0f172a; /* slate-900 */
            --sidebar-fg: #e5e7eb; /* gray-200 */
            --sidebar-fg-muted: #94a3b8; /* slate-400 */
            --toolbar-bg: #111827; /* gray-900 */
            --toolbar-border: #1f2937; /* gray-800 */
            --surface: #0b1220; /* deep dark */
            --card: #111827; /* gray-900 */
            --card-border: #1f2937; /* gray-800 */
            --text: #e5e7eb;
            --muted: #9ca3af;
            --primary: #2563eb;
        }
        html, body { height: 100%; }
        body { margin: 0; background: var(--surface); color: var(--text); font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Inter,Arial,sans-serif; }
        .admin-shell { display: grid; grid-template-columns: 260px 1fr; grid-template-rows: 56px 1fr; height: 100vh; }
        .admin-sidebar { grid-row: 1 / span 2; background: var(--sidebar-bg); color: var(--sidebar-fg); border-right: 1px solid var(--toolbar-border); display: flex; flex-direction: column; }
        .admin-brand { display: flex; align-items: center; gap: 8px; padding: 14px 16px; border-bottom: 1px solid var(--toolbar-border); font-weight: 600; }
        .admin-menu { padding: 8px; overflow: auto; }
        .admin-menu a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; color: var(--sidebar-fg); text-decoration: none; border-radius: 8px; }
        .admin-menu a:hover { background: rgba(255,255,255,0.06); }
        .admin-menu .muted { color: var(--sidebar-fg-muted); font-size: 12px; text-transform: uppercase; letter-spacing: .06em; padding: 8px 12px; }
        .admin-toolbar { grid-column: 2; height: 56px; display: flex; align-items: center; gap: 12px; padding: 0 16px; background: var(--toolbar-bg); border-bottom: 1px solid var(--toolbar-border); }
        .admin-content { grid-column: 2; padding: 16px; overflow: auto; }
        .admin-card { background: var(--card); border: 1px solid var(--card-border); border-radius: 10px; }
        .toolbar-btn { background: #1f2937; color: var(--text); padding: 8px 12px; border: 1px solid var(--card-border); border-radius: 8px; text-decoration: none; }
        .toolbar-btn:hover { background: #273245; }
        .search { flex: 1; display: flex; gap: 8px; }
        .search input { flex: 1; background: #0b1220; border: 1px solid var(--card-border); color: var(--text); border-radius: 8px; padding: 8px 10px; }
        .muted { color: var(--muted); }
        .list-table { width: 100%; border-collapse: collapse; }
        .list-table th, .list-table td { border-bottom: 1px solid var(--card-border); padding: 10px 12px; text-align: left; }
        .kpi { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; margin-bottom: 12px; }
        .kpi .card { padding: 14px; border-radius: 10px; background: var(--card); border: 1px solid var(--card-border); }
    </style>
    @stack('head')
    @stack('styles')
    @stack('scripts-head')
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            @include('admin.partials.sidebar')
        </aside>
        <header class="admin-toolbar">
            @include('admin.partials.toolbar')
        </header>
        <main class="admin-content">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>


