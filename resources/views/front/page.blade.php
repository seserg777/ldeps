@extends('share.layouts.base')

@php
    $componentType = $pageData['componentType'] ?? 'page';
    $menuItem = $pageData['menuItem'] ?? null;
@endphp

@section('title', $menuItem['title'] ?? 'Page')
@section('description', $pageData['siteDescription'] ?? '')

@section('content')
  <div class="page page-{{ $componentType }}">
    <section class="top">
    <div class="container">
            {{-- Top menu - dynamic based on modules --}}
            <x-menu name="main-menu-add" :menus="$renderedMenus ?? []" class="menu-top" />
            
            {{-- Modules: Top position --}}
            @include('share.layouts.partials.modules_position', ['position' => 'top'])
        </div>
    </section>
    
    <header>
        <div class="container">
            <div class="row">
                <div class="col-2">
                    <a href="/" class="logo">
                        <img src="{{ asset('images/logo.svg') }}" alt="Logo">
                    </a>
                </div>
            
                <div class="col-7">
                    {{-- Main menu - dynamic based on modules --}}
                    <x-menu name="mainmenu-rus" :menus="$renderedMenus ?? []" class="menu-main" />
                </div>

                <div class="col-2">
                    @include('share.layouts.partials.search')
                </div>

                <div class="col-1">
                    <x-user-modal-login class="ml-4" />
                </div>
            </div>
            
            {{-- Modules: Header position --}}
            @include('share.layouts.partials.modules_position', ['position' => 'header'])
        </div>
    </header>

    {{-- Page content --}}
    <main>
        {{-- Modules: Content-top position --}}
        <div class="container">
            @include('share.layouts.partials.modules_position', ['position' => 'content-top'])
        </div>
        
        <div class="container">
            <div class="row">
                <div class="col-12">
                    {{-- Dynamic content based on component type --}}
                    @if($componentType === 'Product' && isset($product))
                        @include('front.products.partials.product-detail', ['product' => $product])
                    @elseif($componentType === 'Content' || $componentType === 'ContentList')
                        <div class="content-area">
                            {!! $pageData['additionalData']['content'] ?? '' !!}
                        </div>
                    @elseif($componentType === 'Exussalebanner' || $componentType === 'ExussalebannerList')
                        <div class="banners-area">
                            {{-- Banners content --}}
                        </div>
                    @else
                        @if($menuItem)
                            <h1>{{ $menuItem['title'] }}</h1>
                        @endif
                        <div class="page-content">
                            {!! $pageContentHtml ?? '' !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Modules: Content-bottom position --}}
        <div class="container">
            @include('share.layouts.partials.modules_position', ['position' => 'content-bottom'])
        </div>
    </main>

    @isset($footerHtml)
      <footer>
        {!! $footerHtml !!}
        
        {{-- Modules: Footer position --}}
        <div class="container">
            @include('share.layouts.partials.modules_position', ['position' => 'footer'])
        </div>
      </footer>
    @endisset
    
    {{-- Modules: Bottom position --}}
    <div class="container">
        @include('share.layouts.partials.modules_position', ['position' => 'bottom'])
    </div>
  </div>
@endsection

@push('scripts')
@endpush

