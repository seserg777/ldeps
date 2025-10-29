@extends('admin.layout')
@section('title','Menu Items: ' . $menuType->title)

@section('content')
    <div class="container">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Menu Items: {{ $menuType->title }}</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage menu items for "{{ $menuType->title }}" type</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.menu.types.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Types
                    </a>
                    <a href="{{ route('admin.menu.items.create', $menuType->menutype) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create Item
                    </a>
                </div>
            </div>
        </div>

        <x-admin-grid
            :items="$menuItems"
            :columns="$columns"
            :sortable-columns="$sortableColumns"
            :actions="$actions"
            :bulk-actions="$bulkActions"
            :filters="$filters"
            :searchable="true"
            :search-placeholder="'Search by title, alias or note...'"
            :pagination="true"
            :title="'Menu Items List'"
            :empty-message="'No menu items found'"
        />
    </div>
@endsection
