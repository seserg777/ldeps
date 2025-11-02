@extends('share.layouts.base')

@php
    $componentType = 'homepage';
@endphp

@section('title', 'Home')
@section('description', 'Online store with a wide range of quality products')

@section('content')
  <div class="page home">
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

                <div class="col-3">
                    <div class="header-actions">
                        @include('share.layouts.partials.search')
                        <x-user-modal-login class="ml-4" />
                    </div>
                </div>
            </div>
            
            {{-- Modules: Header position --}}
            @include('share.layouts.partials.modules_position', ['position' => 'header'])
        </div>
    </header>

    {{-- Homepage content --}}
    <main>
        {{-- Modules: Content-top position --}}
        <div class="container">
            @include('share.layouts.partials.modules_position', ['position' => 'content-top'])
        </div>
        
        <div class="container">
            <div class="row">
                <div class="col-12">
                    {!! $homepageHtml ?? '' !!}
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-12">
                    @include('share.layouts.partials.products_module', ['type' => 'random', 'limit' => 3])
                </div>
            </div>
        </div>
        
        {{-- Modules: Content-bottom position --}}
        <div class="container">
            @include('share.layouts.partials.modules_position', ['position' => 'content-bottom'])
        </div>
    </main>

    <footer>
        <div class="container">
            {{-- Footer menu - dynamic based on modules --}}
            <x-menu name="footer" :menus="$renderedMenus ?? []" class="menu-footer" />
            
            {{-- Modules: Footer position --}}
            @include('share.layouts.partials.modules_position', ['position' => 'footer'])
        </div>
    </footer>
    
    {{-- Modules: Bottom position --}}
    <div class="container">
        @include('share.layouts.partials.modules_position', ['position' => 'bottom'])
    </div>
  </div>
@endsection

@push('scripts')
@endpush
