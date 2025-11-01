{{-- Product Modifications Table (Frontend) --}}
@php
    $attributes = $product->productAttributes ?? collect();
@endphp

@if($attributes->count() > 0)
    <div class="modifications-section mt-6">
        <h3 class="text-xl font-semibold mb-4">Модифікації:</h3>
        
        <div class="overflow-x-auto">
            <table class="modifications-table w-full border-collapse">
                <thead>
                    <tr class="bg-white">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-gray-300">#</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-gray-300">Найменування</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-gray-300">Код</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-gray-300">Наявність</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b-2 border-gray-300">Од.виміру</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 border-b-2 border-gray-300 bg-gray-50">Ціна дилерська</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700 border-b-2 border-gray-300 bg-gray-50">Ціна роздрібна</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border-b-2 border-gray-300"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attributes as $index => $attr)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-network-wired text-gray-500"></i>
                                    <span class="text-sm font-medium">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-700">{{ $attr->manufacturer_code ?: $attr->ean ?: '-' }}</span>
                                    @if($attr->manufacturer_code || $attr->ean)
                                        <button type="button" 
                                                onclick="navigator.clipboard.writeText('{{ $attr->manufacturer_code ?: $attr->ean }}')"
                                                class="text-gray-400 hover:text-gray-600"
                                                title="Копіювати код">
                                            <i class="far fa-copy text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-green-600 font-medium">
                                    @if($attr->count > 0)
                                        В наявності
                                    @else
                                        Під замовлення
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $attr->unit == 1 ? 'шт.' : ($attr->unit == 2 ? 'м.' : ($attr->unit == 3 ? 'км.' : '-')) }}
                            </td>
                            <td class="px-4 py-3 text-right bg-gray-50">
                                @if($attr->buy_price > 0)
                                    <div class="text-base font-bold text-gray-900">{{ number_format($attr->buy_price, 2) }} ₴</div>
                                    <div class="text-xs text-gray-500">{{ number_format($attr->buy_price / 42, 2) }} $</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right bg-gray-50">
                                @if($attr->retail_price > 0)
                                    <div class="text-base font-bold text-gray-900">{{ number_format($attr->retail_price, 2) }} ₴</div>
                                    <div class="text-xs text-gray-500">{{ number_format($attr->retail_price / 42, 2) }} $</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" 
                                        class="inline-flex items-center px-3 py-1.5 text-sm text-green-600 border border-green-300 rounded hover:bg-green-50 transition"
                                        onclick="alert('Повідомити про наявність')">
                                    <i class="far fa-bell mr-1.5"></i>
                                    Повідомити про наявність
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <style>
        .modifications-table {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .modifications-table thead th {
            background: #f9fafb;
        }
        
        .modifications-table tbody tr:last-child {
            border-bottom: none;
        }
    </style>
@endif

