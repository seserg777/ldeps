@extends('layouts.app')

@section('title', 'Категорії товарів')

@section('products-count', $categories->count())

@section('sidebar')
    <div class="filter-sidebar">
        <h5><i class="fas fa-th-large me-2"></i>Категорії</h5>
        <p class="text-muted">Оберіть категорію для перегляду товарів</p>
    </div>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Категорії товарів</h4>
        <span class="badge bg-primary fs-6">{{ $categories->count() }} категорій</span>
    </div>

    <div class="row">
        @forelse($categories as $category)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card category-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-folder me-2"></i>{{ $category->name }}
                        </h5>
                        <p class="card-text text-muted">
                            {{ Str::limit($category->description, 100) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-secondary">
                                {{ $category->products_count }} товарів
                            </span>
                            <a href="{{ route('categories.show', $category->category_id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Переглянути
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Категорії не знайдені</h4>
                    <p class="text-muted">На даний момент немає доступних категорій</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection

@push('styles')
<style>
    .category-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #e9ecef;
    }
    
    .category-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    
    .category-card .card-title {
        color: #495057;
        font-weight: 600;
    }
</style>
@endpush


