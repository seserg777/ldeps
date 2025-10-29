@extends('share.layouts.base')

@section('content')
<div class="container py-4">
    @yield('page-content')
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

@push('scripts')
<script>
// Global cart modal functions
window.openCartModal = function() {
    if (window.cartModalInstance) {
        window.cartModalInstance.open()
    }
}

window.closeCartModal = function() {
    if (window.cartModalInstance) {
        window.cartModalInstance.close()
    }
}

// Initialize Vue app
document.addEventListener('DOMContentLoaded', function() {
    const { createApp } = Vue;
    
    const app = createApp({
        components: {
            CartModal: window.CartModal || {}
        }
    });
    
    app.mount('#app');
});
</script>
@endpush
