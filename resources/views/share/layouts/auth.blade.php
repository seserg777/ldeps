@extends('share.layouts.base')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    @yield('page-content')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
