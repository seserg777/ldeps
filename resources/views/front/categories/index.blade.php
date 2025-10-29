@extends('share.layouts.app')

@section('title', 'Категории товаров')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Категории товаров</h1>
        <p class="text-gray-600">Выберите интересующую вас категорию</p>
    </div>

    <!-- Categories Grid -->
    @if($categories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($categories as $category)
                <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <a href="{{ route('category.show', $category->category_id) }}" class="block">
                        <div class="p-6">
                            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full">
                                <i class="fas fa-tag text-blue-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">
                                {{ $category->name }}
                            </h3>
                            @if($category->description)
                                <p class="text-gray-600 text-sm text-center">
                                    {{ Str::limit($category->description, 100) }}
                                </p>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Категории не найдены</h3>
            <p class="text-gray-600">В данный момент нет доступных категорий товаров</p>
        </div>
    @endif
</div>
@endsection