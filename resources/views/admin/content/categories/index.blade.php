@extends('admin.layout')
@section('title','Content Categories')

@section('content')
    <div class="container">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Content Categories</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage content categories in the system</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.categories.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create Category
                    </a>
                </div>
            </div>
        </div>

        <x-admin-grid
            :items="$categories"
            :columns="$columns"
            :sortable-columns="$sortableColumns"
            :actions="$actions"
            :bulk-actions="$bulkActions"
            :filters="$filters"
            :searchable="true"
            :search-placeholder="'Search by title, alias or note...'"
            :pagination="true"
            :title="'Categories List'"
            :empty-message="'No categories found'"
        />
    </div>
@endsection
