{{-- 
  Partial for rendering modules at a specific position
  Usage: @include('share.layouts.partials.modules_position', ['position' => 'header', 'activeMenuId' => $activeMenuId ?? null])
--}}

@php
    $position = $position ?? 'content-top';
    $activeMenuId = $activeMenuId ?? \App\Helpers\ModuleHelper::getActiveMenuId();
@endphp

<x-module-renderer :position="$position" :active-menu-id="$activeMenuId" />

