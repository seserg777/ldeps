@extends('admin.layout')
@section('title','Редактировать товар')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.products') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Назад к списку
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg" x-data="{ activeTab: 'description-uk' }">
        {{-- Main Tabs --}}
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto">
                <button type="button" @click="activeTab = 'description-uk'" 
                        :class="activeTab === 'description-uk' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center space-x-2">
                    <span>🇺🇦</span>
                    <span>Опис (uk)</span>
                </button>
                <button type="button" @click="activeTab = 'description-ru'" 
                        :class="activeTab === 'description-ru' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center space-x-2">
                    <span>🇷🇺</span>
                    <span>Опис (ru)</span>
                </button>
                <button type="button" @click="activeTab = 'description-en'" 
                        :class="activeTab === 'description-en' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm flex items-center space-x-2">
                    <span>🇬🇧</span>
                    <span>Description (en)</span>
                </button>
                <button type="button" @click="activeTab = 'info'" 
                        :class="activeTab === 'info' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Інформація про товар
                </button>
                <button type="button" @click="activeTab = 'attributes'" 
                        :class="activeTab === 'attributes' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Атрибут
                </button>
                <button type="button" @click="activeTab = 'characteristics'" 
                        :class="activeTab === 'characteristics' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Характеристики
                </button>
                <button type="button" @click="activeTab = 'stock'" 
                        :class="activeTab === 'stock' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Залишки
                </button>
                <button type="button" @click="activeTab = 'photos'" 
                        :class="activeTab === 'photos' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Фото
                </button>
                <button type="button" @click="activeTab = 'video'" 
                        :class="activeTab === 'video' ? 'border-blue-500 text-blue-600 bg-blue-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                    Відео
                </button>
            </nav>
        </div>

        <form method="POST" action="{{ route('admin.products.update', $product->product_id) }}">
            @csrf
            @method('PUT')
            
            {{-- Description UK Tab --}}
            <div x-show="activeTab === 'description-uk'" class="px-6 py-6 space-y-6">
                <div>
                    <label for="name_uk-UA" class="block text-sm font-medium text-gray-700 mb-2">
                        Назва товару *
                    </label>
                    <input type="text" 
                           name="name_uk-UA" 
                           id="name_uk-UA"
                           value="{{ old('name_uk-UA', $product->{'name_uk-UA'}) }}"
                           required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="short_description_uk-UA" class="block text-sm font-medium text-gray-700 mb-2">
                        Короткий опис
                    </label>
                    <textarea name="short_description_uk-UA" 
                              id="short_description_uk-UA"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('short_description_uk-UA', $product->{'short_description_uk-UA'}) }}</textarea>
                </div>
                
                <div>
                    <label for="description_uk-UA" class="block text-sm font-medium text-gray-700 mb-2">
                        Повний опис
                    </label>
                    <textarea name="description_uk-UA" 
                              id="description_uk-UA"
                              rows="10"
                              class="tinymce-editor">{{ old('description_uk-UA', $product->{'description_uk-UA'}) }}</textarea>
                </div>
            </div>
            
            {{-- Description RU Tab --}}
            <div x-show="activeTab === 'description-ru'" class="px-6 py-6 space-y-6">
                <div>
                    <label for="name_ru-UA" class="block text-sm font-medium text-gray-700 mb-2">
                        Название товара
                    </label>
                    <input type="text" 
                           name="name_ru-UA" 
                           id="name_ru-UA"
                           value="{{ old('name_ru-UA', $product->{'name_ru-UA'}) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="short_description_ru-UA" class="block text-sm font-medium text-gray-700 mb-2">
                        Краткое описание
                    </label>
                    <textarea name="short_description_ru-UA" 
                              id="short_description_ru-UA"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('short_description_ru-UA', $product->{'short_description_ru-UA'}) }}</textarea>
                </div>
                
                <div>
                    <label for="description_ru-UA" class="block text-sm font-medium text-gray-700 mb-2">
                        Полное описание
                    </label>
                    <textarea name="description_ru-UA" 
                              id="description_ru-UA"
                              rows="10"
                              class="tinymce-editor">{{ old('description_ru-UA', $product->{'description_ru-UA'}) }}</textarea>
                </div>
            </div>
            
            {{-- Description EN Tab --}}
            <div x-show="activeTab === 'description-en'" class="px-6 py-6 space-y-6">
                <div>
                    <label for="name_en-GB" class="block text-sm font-medium text-gray-700 mb-2">
                        Product Name
                    </label>
                    <input type="text" 
                           name="name_en-GB" 
                           id="name_en-GB"
                           value="{{ old('name_en-GB', $product->{'name_en-GB'}) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="short_description_en-GB" class="block text-sm font-medium text-gray-700 mb-2">
                        Short Description
                    </label>
                    <textarea name="short_description_en-GB" 
                              id="short_description_en-GB"
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">{{ old('short_description_en-GB', $product->{'short_description_en-GB'}) }}</textarea>
                </div>
                
                <div>
                    <label for="description_en-GB" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Description
                    </label>
                    <textarea name="description_en-GB" 
                              id="description_en-GB"
                              rows="10"
                              class="tinymce-editor">{{ old('description_en-GB', $product->{'description_en-GB'}) }}</textarea>
                </div>
            </div>
            
            {{-- Product Info Tab --}}
            <div x-show="activeTab === 'info'" class="px-6 py-6 space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="product_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Код товару
                        </label>
                        <input type="text" 
                               name="product_code" 
                               id="product_code"
                               value="{{ old('product_code', $product->product_code) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="product_ean" class="block text-sm font-medium text-gray-700 mb-2">
                            EAN / Barcode
                        </label>
                        <input type="text" 
                               name="product_ean" 
                               id="product_ean"
                               value="{{ old('product_ean', $product->product_ean) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="product_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Ціна *
                        </label>
                        <input type="number" 
                               name="product_price" 
                               id="product_price" 
                               value="{{ old('product_price', $product->product_price) }}"
                               step="0.01"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="product_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Кількість
                        </label>
                        <input type="number" 
                               name="product_quantity" 
                               id="product_quantity"
                               value="{{ old('product_quantity', $product->product_quantity) }}"
                               step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="product_manufacturer_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Виробник
                        </label>
                        <select name="product_manufacturer_id" 
                                id="product_manufacturer_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Оберіть виробника</option>
                            @foreach(\App\Models\Manufacturer::orderBy('name_uk-UA')->get() as $manufacturer)
                                <option value="{{ $manufacturer->manufacturer_id }}" 
                                        {{ old('product_manufacturer_id', $product->product_manufacturer_id) == $manufacturer->manufacturer_id ? 'selected' : '' }}>
                                    {{ $manufacturer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" 
                           name="product_publish" 
                           id="product_publish" 
                           value="1"
                           {{ old('product_publish', $product->product_publish) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="product_publish" class="ml-2 block text-sm text-gray-900">
                        Опублікувати товар
                    </label>
                </div>
            </div>
            
            {{-- Attributes Tab --}}
            <div x-show="activeTab === 'attributes'" class="px-6 py-6">
                <p class="text-gray-500 italic">Attributes section - coming soon</p>
            </div>

            {{-- Characteristics Tab --}}
            <div x-show="activeTab === 'characteristics'" class="px-6 py-6">
                @include('admin.products.partials.characteristics', ['product' => $product])
            </div>
            
            {{-- Stock Tab --}}
            <div x-show="activeTab === 'stock'" class="px-6 py-6">
                <p class="text-gray-500 italic">Stock management - coming soon</p>
            </div>
            
            {{-- Photos Tab --}}
            <div x-show="activeTab === 'photos'" class="px-6 py-6">
                <p class="text-gray-500 italic">Photo gallery - coming soon</p>
            </div>
            
            {{-- Video Tab --}}
            <div x-show="activeTab === 'video'" class="px-6 py-6">
                <p class="text-gray-500 italic">Video gallery - coming soon</p>
            </div>

            {{-- Save Button (always visible) --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.products') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-save mr-2"></i>
                    Зберегти зміни
                </button>
            </div>
        </form>
    </div>
@endsection

@include('admin.partials.tinymce')
