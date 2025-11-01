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
    </main>

    @isset($footerHtml)
      <footer>{!! $footerHtml !!}</footer>
    @endisset
  </div>
@endsection

@push('scripts')
@endpush
