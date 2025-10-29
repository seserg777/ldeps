@extends('share.layouts.base')

@section('content')
@yield('page-content')
@endsection

@push('vue-components')
<cart-modal
    :cart-index-url="'{{ route('cart.index') }}'"
    :cart-modal-url="'{{ route('cart.modal') }}'"
    :cart-remove-url="'{{ route('cart.remove') }}'"
    :csrf-token="'{{ csrf_token() }}'"
></cart-modal>
@endpush
