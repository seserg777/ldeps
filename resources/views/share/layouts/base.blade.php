<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Performance meta tags -->
    <meta name="theme-color" content="#0d6efd">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- DNS prefetch for external resources -->
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//code.jquery.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <title>@yield('title', 'Каталог товарів') - {{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="@yield('description', 'Каталог товарів')">

    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"></noscript>

    <!-- Font Awesome - load only needed icons -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></noscript>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Vite: dev with robust fallback to build manifest -->
    <?php
        $hotPath = public_path('hot');
        $manifestPath = public_path('build/manifest.json');
        $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
        $entry = $manifest['resources/js/app.js'] ?? null;

        // Preload CSS from build if available (safe to include in dev too)
        if ($entry && !empty($entry['css'])) {
            foreach ($entry['css'] as $css) {
                $cssUrl = url('build/' . $css);
                echo '<link rel="stylesheet" href="' . $cssUrl . '">';
            }
        }

        if (file_exists($hotPath)) {
            $url = trim(file_get_contents($hotPath));
            $buildJs = $entry ? url('build/' . $entry['file']) : '';
            echo '<script type="module">(async()=>{try{await import("' . $url . '/@vite/client");await import("' . $url . '/resources/js/app.js");}catch(e){' . ($buildJs ? 'await import("' . $buildJs . '");' : '') . '}})();</script>';
        } else if ($entry) {
            $buildJs = url('build/' . $entry['file']);
            echo '<script type="module">import("' . $buildJs . '")</script>';
        }
    ?>

    <!-- Google Fonts - Rubik with display=swap for better performance -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&display=swap"></noscript>
    <style>
        /* Enforce font-display swap for faster text rendering */
        @font-face {
            font-family: 'Rubik';
            font-style: normal;
            font-weight: 300 700;
            font-display: swap;
            src: local('Rubik');
        }
    </style>

    @stack('styles')

    <!-- Critical CSS inline -->
    <style>
        /* Critical above-the-fold styles with Rubik font */
        body {
            font-family: 'Rubik', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.5rem;
        }

        .main-content {
            min-height: calc(100vh - 200px);
        }
    </style>
</head>
@php
    // Determine active menu item id based on current SEO alias (first path segment, without .html)
    $activeMenuId = null;
    try {
        $path = request()->path();
        $first = $path === '/' || $path === '' ? '' : explode('/', $path)[0];
        if ($first) {
            $alias = preg_replace('/\.html$/', '', $first);
            $active = \App\Models\Menu\Menu::where('alias', $alias)->where('published', 1)->first();
            if ($active) { $activeMenuId = (int) $active->id; }
        }
    } catch (\Throwable $e) {
        $activeMenuId = null;
    }
@endphp
<body class="controller-{{ strtolower(class_basename(request()->route()->getController())) }} component-{{ $componentClass ?? 'default' }}{{ $activeMenuId ? ' itemid-' . $activeMenuId : '' }}">
    @yield('content')

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
