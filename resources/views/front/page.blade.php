@extends('share.layouts.base')

@php
    $componentType = $pageData['componentType'] ?? 'default';
    // Map component types to proper names for CSS classes
    $componentClass = match($componentType) {
        'Content' => 'content',
        'ContentList' => 'content-list', 
        'Exussalebanner' => 'banner',
        'ExussalebannerList' => 'banner-list',
        default => 'default'
    };
@endphp

@section('title', $pageData['menuItem']['title'] . ' - ' . $pageData['siteName'])

@section('content')
  <div class="page {{ $componentClass }}">
    {{-- Top menu SSR --}}
    <nav class="site-menu menu-top">
      {!! $menuTopHtml ?? '' !!}
    </nav>

    {{-- Main menu SSR --}}
    <nav class="site-menu menu-main">
      {!! $menuMainHtml ?? '' !!}
    </nav>

    <main>
      {!! $pageContentHtml ?? '' !!}
    </main>

    @isset($footerHtml)
      <footer>{!! $footerHtml !!}</footer>
    @endisset
  </div>
@endsection
