@extends('share.layouts.base')

@section('content')
<div class="container py-4">
    @yield('page-content')
</div>
@endsection

@push('vue-components')
@endpush

@push('scripts')
<script>
// Инициализация и обработчики перенесены в resources/js/app.js (Vite)
</script>
@endpush
