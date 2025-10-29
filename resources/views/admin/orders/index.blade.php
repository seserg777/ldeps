@extends('admin.layout')
@section('title','Заказы')

@section('content')
    <div class="container">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Заказы</h1>
                    <p class="mt-1 text-sm text-gray-600">Управление заказами клиентов</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-download mr-2"></i>
                        Экспорт
                    </button>
                </div>
            </div>
        </div>

        <x-admin-grid
            :items="$orders"
            :columns="$columns"
            :sortable-columns="$sortableColumns"
            :actions="$actions"
            :bulk-actions="$bulkActions"
            :searchable="true"
            :search-placeholder="'Поиск по номеру заказа...'"
            :pagination="true"
            :per-page="20"
            :title="'Список заказов'"
            :empty-message="'Заказы не найдены'"
        />
    </div>
@endsection