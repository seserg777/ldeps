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
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">{{ $pageData['menuItem']['title'] }}</h1>
            
            @if($pageData['componentType'] === 'Content')
                <content-component 
                    menu-items="{{ json_encode($pageData['menuItems'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    language="{{ $pageData['language'] }}"
                    site-name="{{ $pageData['siteName'] }}"
                    site-description="{{ $pageData['siteDescription'] }}"
                    menu-item="{{ json_encode($pageData['menuItem'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    link-params="{{ json_encode($pageData['linkParams'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    article="{{ json_encode($pageData['additionalData']['article'] ?? null, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                ></content-component>
            @elseif($pageData['componentType'] === 'ContentList')
                <content-list-component 
                    menu-items="{{ json_encode($pageData['menuItems'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    language="{{ $pageData['language'] }}"
                    site-name="{{ $pageData['siteName'] }}"
                    site-description="{{ $pageData['siteDescription'] }}"
                    menu-item="{{ json_encode($pageData['menuItem'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    link-params="{{ json_encode($pageData['linkParams'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    articles="{{ json_encode($pageData['additionalData']['articles'] ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    pagination="{{ json_encode($pageData['additionalData']['pagination'] ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                ></content-list-component>
            @elseif($pageData['componentType'] === 'Exussalebanner')
                <exussalebanner-component 
                    menu-items="{{ json_encode($pageData['menuItems'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    language="{{ $pageData['language'] }}"
                    site-name="{{ $pageData['siteName'] }}"
                    site-description="{{ $pageData['siteDescription'] }}"
                    menu-item="{{ json_encode($pageData['menuItem'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    link-params="{{ json_encode($pageData['linkParams'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    banner="{{ json_encode($pageData['additionalData']['banner'] ?? null, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                ></exussalebanner-component>
            @elseif($pageData['componentType'] === 'ExussalebannerList')
                <exussalebanner-list-component 
                    menu-items="{{ json_encode($pageData['menuItems'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    language="{{ $pageData['language'] }}"
                    site-name="{{ $pageData['siteName'] }}"
                    site-description="{{ $pageData['siteDescription'] }}"
                    menu-item="{{ json_encode($pageData['menuItem'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    link-params="{{ json_encode($pageData['linkParams'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    banners="{{ json_encode($pageData['additionalData']['banners'] ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    pagination="{{ json_encode($pageData['additionalData']['pagination'] ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                ></exussalebanner-list-component>
            @elseif($pageData['componentType'] === 'Exussalebanner' && isset($pageData['additionalData']['banner']))
                {{-- Fallback kept for safety, main branch already passes banner --}}
                <exussalebanner-component 
                    menu-items="{{ json_encode($pageData['menuItems'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    language="{{ $pageData['language'] }}"
                    site-name="{{ $pageData['siteName'] }}"
                    site-description="{{ $pageData['siteDescription'] }}"
                    menu-item="{{ json_encode($pageData['menuItem'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    link-params="{{ json_encode($pageData['linkParams'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                    banner="{{ json_encode($pageData['additionalData']['banner'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}"
                ></exussalebanner-component>
            @else
                <div class="alert alert-info">
                    <h4>Страница: {{ $pageData['menuItem']['title'] }}</h4>
                    <p>Компонент для этого типа страницы пока не настроен.</p>
                    @if($pageData['linkParams'])
                        <p><strong>Параметры ссылки:</strong></p>
                        <ul>
                            @foreach($pageData['linkParams'] as $key => $value)
                                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
