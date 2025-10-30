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

@push('vue-components')
<page-component
    language='{{ $pageData['language'] }}'
    site-name='{{ $pageData['siteName'] }}'
    site-description='{{ $pageData['siteDescription'] }}'
    title='{{ $pageData['menuItem']['title'] }}'
    menu-item='{{ json_encode($pageData['menuItem'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}'
    link-params='{{ json_encode($pageData['linkParams'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}'
    @if(($pageData['componentType'] ?? '') === 'Content')
      article='{{ json_encode($pageData['additionalData']['article'] ?? null, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}'
    @elseif(($pageData['componentType'] ?? '') === 'ContentList')
      articles='{{ json_encode($pageData['additionalData']['articles'] ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}'
      pagination='{{ json_encode($pageData['additionalData']['pagination'] ?? [], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}'
    @endif
></page-component>
@endpush
