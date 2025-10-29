@extends('share.layouts.app')

@section('title', 'Акції з тегом: ' . $tag->name)
@section('description', 'Акції та розпродажі з тегом: ' . $tag->name)

@section('content')
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Головна</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sale-banners.index') }}">Акції</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $tag->name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h2 mb-3">
                <i class="fas fa-tag me-2 text-primary"></i>Акції з тегом: {{ $tag->name }}
            </h1>
            <p class="text-muted">Знайдено {{ $saleBanners->total() }} акцій з тегом "{{ $tag->name }}"</p>
        </div>
    </div>

    <!-- Tags Filter -->
    @if($tags->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-filter me-2"></i>Фільтр за категоріями
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('sale-banners.index') }}" 
                               class="btn btn-outline-primary">
                                Всі акції
                            </a>
                            @foreach($tags as $tagItem)
                                <a href="{{ route('sale-banners.tag', $tagItem->alias) }}" 
                                   class="btn btn-outline-primary {{ $tagItem->id == $tag->id ? 'active' : '' }}">
                                    {{ $tagItem->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Sale Banners Grid -->
    @if($saleBanners->count() > 0)
        <div class="row">
            @foreach($saleBanners as $banner)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm sale-banner-card">
                        <div class="position-relative">
                            @if($banner->getLocalizedField('image'))
                                <img src="{{ $banner->getLocalizedField('image') }}" 
                                     class="card-img-top" 
                                     alt="{{ $banner->getLocalizedField('title') }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-percentage fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <!-- Sale Badge -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-danger">
                                    <i class="fas fa-fire me-1"></i>Акція
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">
                                <a href="{{ $banner->getUrl() }}" class="text-decoration-none">
                                    {{ $banner->getLocalizedField('title') }}
                                </a>
                            </h5>
                            
                            <p class="card-text text-muted flex-grow-1">
                                {{ Str::limit(strip_tags($banner->getLocalizedField('introtext')), 100) }}
                            </p>
                            
                            <!-- Sale Period -->
                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $banner->sale_start->format('d.m.Y') }} - {{ $banner->sale_end->format('d.m.Y') }}
                                </small>
                            </div>
                            
                            <!-- Tags -->
                            @if($banner->tags->count() > 0)
                                <div class="mb-3">
                                    @foreach($banner->tags->take(2) as $bannerTag)
                                        <span class="badge bg-light text-dark me-1 {{ $bannerTag->id == $tag->id ? 'bg-primary text-white' : '' }}">
                                            {{ $bannerTag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="mt-auto">
                                <a href="{{ $banner->getUrl() }}" class="btn btn-primary w-100">
                                    <i class="fas fa-eye me-2"></i>Детальніше
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                {{ $saleBanners->links() }}
            </div>
        </div>
    @else
        <!-- No Sale Banners -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-tag fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Акції не знайдено</h4>
                        <p class="text-muted">Немає активних акцій з тегом "{{ $tag->name }}"</p>
                        <a href="{{ route('sale-banners.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Всі акції
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('styles')
<style>
.sale-banner-card {
    transition: transform 0.2s ease-in-out;
}

.sale-banner-card:hover {
    transform: translateY(-5px);
}

.sale-banner-card .card-img-top {
    transition: transform 0.2s ease-in-out;
}

.sale-banner-card:hover .card-img-top {
    transform: scale(1.05);
}
</style>
@endpush
