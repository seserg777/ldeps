@extends('share.layouts.base')

@php
    $componentType = 'homepage';
@endphp

@section('title', 'Главная страница')
@section('description', 'Интернет-магазин с широким ассортиментом качественных товаров')

@section('content')
  <div class="page home">
    <section class="top">
        <div class="container">
            {{-- Top menu SSR --}}
            <nav class="site-menu menu-top menu-main-menu-add">
                {!! $menuTopHtml ?? '' !!}
            </nav>
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
                    {{-- Main menu SSR --}}
                    <nav class="site-menu menu-main menu-mainmenu-rus">
                        {!! $menuMainHtml ?? '' !!}
                    </nav>
                </div>

                <div class="col-3">
                    @include('share.layouts.partials.search')
                </div>
            </div>
        </div>
    </header>

    {{-- Homepage content --}}
    <main>
      {!! $homepageHtml ?? '' !!}
      @include('share.layouts.partials.products_module', ['type' => 'random', 'limit' => 3])
    </main>

    @isset($footerHtml)
      <footer>{!! $footerHtml !!}</footer>
    @endisset
  </div>
@endsection

@push('scripts')
@endpush
