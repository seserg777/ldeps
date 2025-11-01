@extends('admin.layout')
@section('title','Редактировать товар')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Редактировать товар</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $product->product_name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.products') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Назад к списку
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('admin.products.update', $product->product_id) }}" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Основная информация</h3>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                {{-- Product Name (multilang) --}}
                @include('admin.components.multilang-field', [
                    'name' => 'name',
                    'label' => 'Product Name',
                    'type' => 'text',
                    'value' => $product,
                    'required' => true
                ])
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="product_code" class="block text-sm font-medium text-gray-700">
                            Product Code
                        </label>
                        <input type="text" 
                               name="product_code" 
                               id="product_code"
                               value="{{ old('product_code', $product->product_code) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="product_ean" class="block text-sm font-medium text-gray-700">
                            EAN / Barcode
                        </label>
                        <input type="text" 
                               name="product_ean" 
                               id="product_ean"
                               value="{{ old('product_ean', $product->product_ean) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="product_price" class="block text-sm font-medium text-gray-700">
                            Цена *
                        </label>
                        <input type="number" 
                               name="product_price" 
                               id="product_price" 
                               value="{{ old('product_price', $product->product_price) }}"
                               step="0.01"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="product_quantity" class="block text-sm font-medium text-gray-700">
                            Количество
                        </label>
                        <input type="number" 
                               name="product_quantity" 
                               id="product_quantity"
                               value="{{ old('product_quantity', $product->product_quantity) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="product_manufacturer_id" class="block text-sm font-medium text-gray-700">
                            Производитель
                        </label>
                        <select name="product_manufacturer_id" 
                                id="product_manufacturer_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Выберите производителя</option>
                            @foreach(\App\Models\Manufacturer::all() as $manufacturer)
                                <option value="{{ $manufacturer->manufacturer_id }}" 
                                        {{ old('product_manufacturer_id', $product->product_manufacturer_id) == $manufacturer->manufacturer_id ? 'selected' : '' }}>
                                    {{ $manufacturer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Short Description (multilang) --}}
                @include('admin.components.multilang-field', [
                    'name' => 'short_description',
                    'label' => 'Short Description',
                    'type' => 'textarea',
                    'value' => $product,
                    'rows' => 3
                ])
                
                {{-- Full Description (multilang with TinyMCE) --}}
                @include('admin.components.multilang-field', [
                    'name' => 'description',
                    'label' => 'Full Description',
                    'type' => 'tinymce',
                    'value' => $product,
                    'rows' => 10
                ])

                <div class="flex items-center">
                    <input type="checkbox" 
                           name="product_publish" 
                           id="product_publish" 
                           value="1"
                           {{ old('product_publish', $product->product_publish) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="product_publish" class="ml-2 block text-sm text-gray-900">
                        Опубликовать товар
                    </label>
                </div>
            </div>

            {{-- Characteristics Section --}}
            <div class="px-6 py-4 border-t border-gray-200 mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Характеристики</h3>
                @include('admin.products.partials.characteristics', ['product' => $product])
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.products') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-save mr-2"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection

@include('admin.partials.tinymce')
