@props([
    'items' => collect(),
    'columns' => [],
    'sortableColumns' => [],
    'sortBy' => null,
    'sortDirection' => 'asc',
    'searchable' => true,
    'searchPlaceholder' => 'Поиск...',
    'actions' => [],
    'bulkActions' => [],
    'pagination' => true,
    'perPage' => 15,
    'showFilters' => false,
    'filters' => [],
    'emptyMessage' => 'Нет данных для отображения',
    'title' => 'Список элементов'
])

@php
    $currentPage = request('page', 1);
    $perPage = request('per_page', $perPage);
    $search = request('search', '');
    $sortBy = request('sort_by', $sortBy);
    $sortDirection = request('sort_direction', $sortDirection);
    
    // Apply search filter
    if ($searchable && $search) {
        $items = $items->filter(function($item) use ($search, $columns) {
            foreach ($columns as $column) {
                if (isset($column['searchable']) && $column['searchable']) {
                    $value = data_get($item, $column['key']);
                    if (is_string($value) && stripos($value, $search) !== false) {
                        return true;
                    }
                }
            }
            return false;
        });
    }
    
    // Apply sorting
    if ($sortBy && in_array($sortBy, $sortableColumns)) {
        $items = $items->sortBy(function($item) use ($sortBy) {
            return data_get($item, $sortBy);
        }, SORT_REGULAR, $sortDirection === 'desc');
    }
    
    // Apply pagination
    if ($pagination) {
        $totalItems = $items->count();
        $totalPages = ceil($totalItems / $perPage);
        $items = $items->forPage($currentPage, $perPage);
    }
@endphp

<div class="bg-white shadow rounded-lg">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
            @if(count($bulkActions) > 0)
                <div class="flex items-center space-x-2">
                    <select class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option>Действия с выбранными</option>
                        @foreach($bulkActions as $action)
                            <option value="{{ $action['key'] }}">{{ $action['label'] }}</option>
                        @endforeach
                    </select>
                    <button class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Применить
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Search and Filters -->
    @if($searchable || $showFilters)
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0 sm:space-x-4">
                @if($searchable)
                    <div class="flex-1 max-w-lg">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}"
                                   placeholder="{{ $searchPlaceholder }}"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                @endif

                @if($showFilters && count($filters) > 0)
                    <div class="flex space-x-2">
                        @foreach($filters as $filter)
                            <select class="text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option>{{ $filter['label'] }}</option>
                                @foreach($filter['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Table -->
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @if(count($bulkActions) > 0)
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                        @endif
                        @foreach($columns as $column)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                @if(in_array($column['key'], $sortableColumns))
                                    <a href="?sort_by={{ $column['key'] }}&sort_direction={{ $sortBy === $column['key'] && $sortDirection === 'asc' ? 'desc' : 'asc' }}" 
                                       class="group inline-flex items-center hover:text-gray-700">
                                        {{ $column['label'] }}
                                        @if($sortBy === $column['key'])
                                            @if($sortDirection === 'asc')
                                                <i class="fas fa-sort-up ml-1"></i>
                                            @else
                                                <i class="fas fa-sort-down ml-1"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort ml-1 text-gray-400 group-hover:text-gray-700"></i>
                                        @endif
                                    </a>
                                @else
                                    {{ $column['label'] }}
                                @endif
                            </th>
                        @endforeach
                        @if(count($actions) > 0)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Действия
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            @if(count($bulkActions) > 0)
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" 
                                           value="{{ $item->id ?? $item->product_id ?? $item->category_id ?? $item->manufacturer_id }}"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </td>
                            @endif
                            @foreach($columns as $column)
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $value = data_get($item, $column['key']);
                                    @endphp
                                    
                                    @if(isset($column['render']) && is_callable($column['render']))
                                        {!! $column['render']($item) !!}
                                    @elseif(isset($column['format']))
                                        @switch($column['format'])
                                            @case('currency')
                                                {{ number_format($value, 2, '.', ' ') }} UAH
                                                @break
                                            @case('date')
                                                {{ $value ? \Carbon\Carbon::parse($value)->format('d.m.Y') : '—' }}
                                                @break
                                            @case('datetime')
                                                {{ $value ? \Carbon\Carbon::parse($value)->format('d.m.Y H:i') : '—' }}
                                                @break
                                            @case('status')
                                                @if(is_bool($value))
                                                    @if($value)
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                            Активен
                                                        </span>
                                                    @else
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                            Неактивен
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        {{ ucfirst($value) }}
                                                    </span>
                                                @endif
                                                @break
                                            @default
                                                {{ $value ?? '—' }}
                                        @endswitch
                                    @else
                                        {{ $value ?? '—' }}
                                    @endif
                                </td>
                            @endforeach
                            @if(count($actions) > 0)
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        @foreach($actions as $action)
                                            @php
                                                $showAction = true;
                                                if (isset($action['condition']) && is_callable($action['condition'])) {
                                                    $showAction = $action['condition']($item);
                                                }
                                            @endphp
                                            @if($showAction)
                                                <a href="{{ $action['url']($item) }}" 
                                                   class="text-{{ $action['class'] }}-600 hover:text-{{ $action['class'] }}-900"
                                                   title="{{ $action['label'] }}">
                                                    <i class="fas fa-{{ $action['icon'] }}"></i>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + (count($actions) > 0 ? 1 : 0) + (count($bulkActions) > 0 ? 1 : 0) }}" 
                                class="px-6 py-12 text-center text-sm text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                    {{ $emptyMessage }}
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($pagination && $totalPages > 1)
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if($currentPage > 1)
                    <a href="?page={{ $currentPage - 1 }}" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Предыдущая
                    </a>
                @endif
                @if($currentPage < $totalPages)
                    <a href="?page={{ $currentPage + 1 }}" 
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Следующая
                    </a>
                @endif
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Показано
                        <span class="font-medium">{{ (($currentPage - 1) * $perPage) + 1 }}</span>
                        -
                        <span class="font-medium">{{ min($currentPage * $perPage, $totalItems) }}</span>
                        из
                        <span class="font-medium">{{ $totalItems }}</span>
                        результатов
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        @if($currentPage > 1)
                            <a href="?page={{ $currentPage - 1 }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif
                        
                        @for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++)
                            <a href="?page={{ $i }}" 
                               class="relative inline-flex items-center px-4 py-2 border text-sm font-medium {{ $i === $currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' }}">
                                {{ $i }}
                            </a>
                        @endfor
                        
                        @if($currentPage < $totalPages)
                            <a href="?page={{ $currentPage + 1 }}" 
                               class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
    @endif
</div>