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
    
    <!-- Vite (manual include for frameworks без встроенной директивы @vite) -->
    <?php
        $hotPath = public_path('hot');
        if (file_exists($hotPath)) {
            $url = trim(file_get_contents($hotPath));
            echo '<script type="module" src="' . $url . '/@vite/client"></script>';
            echo '<script type="module" src="' . $url . '/resources/js/app.js"></script>';
        } else {
            $manifestPath = public_path('build/manifest.json');
            if (file_exists($manifestPath)) {
                $manifest = json_decode(file_get_contents($manifestPath), true);
                $entry = $manifest['resources/js/app.js'] ?? null;
                if ($entry) {
                    // Always serve from build because current web root is project root
                    if (!empty($entry['css'])) {
                        foreach ($entry['css'] as $css) {
                            $cssPublic = url('build/' . $css);
                            $cssRoot = url('build/' . $css);
                            echo '<script>(function(){var l=document.createElement("link");l.rel="stylesheet";l.href="' . $cssPublic . '";l.onerror=function(){l.href="' . $cssRoot . '"};document.head.appendChild(l)})();</script>';
                        }
                    }
                    $jsPublic = url('build/' . $entry['file']);
                    $jsRoot = url('build/' . $entry['file']);
                    echo '<script type="module">import("' . $jsPublic . '").catch(()=>import("' . $jsRoot . '"))</script>';
                }
            }
        }
    ?>

    <!-- Google Fonts - Rubik with display=swap for better performance -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&display=swap"></noscript>

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
<body>
    <!-- Vue Root -->
    <div id="vue-root">
        <!-- Header -->
        @include('share.layouts.partials.header')
    </div>

    <!-- Breadcrumbs -->
    @hasSection('breadcrumbs')
        @yield('breadcrumbs')
    @endif

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    @include('share.layouts.partials.footer')

    <!-- Modals -->
    @stack('modals')

    <!-- Scripts -->
    @stack('scripts')

    <!-- Vue Components -->
    <div id="vue-root-modals">
        @stack('vue-components')
    </div>
</body>
</html>
