@props(['name', 'menus' => [], 'class' => ''])

@php
    // Try to find menu by name in the rendered menus array
    $menuHtml = $menus[$name] ?? '';
@endphp

@if($menuHtml)
    <nav {{ $attributes->merge(['class' => 'site-menu ' . $class]) }}>
        {!! $menuHtml !!}
    </nav>
@endif

