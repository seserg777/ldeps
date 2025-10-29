@extends('share.layouts.base')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        @hasSection('sidebar')
            <div class="col-lg-3 col-md-4 mb-4">
                @yield('sidebar')
            </div>
        @endif
        
        <!-- Main Content -->
        <div class="{{ isset($sidebar) ? 'col-lg-9 col-md-8' : 'col-12' }}">
            @yield('page-content')
        </div>
    </div>
</div>
@endsection

@push('vue-components')
<cart-modal
    :cart-index-url="'{{ route('cart.index') }}'"
    :cart-modal-url="'{{ route('cart.modal') }}'"
    :cart-remove-url="'{{ route('cart.remove') }}'"
    :csrf-token="'{{ csrf_token() }}'"
></cart-modal>
@endpush
