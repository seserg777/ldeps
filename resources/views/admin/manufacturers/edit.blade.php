@extends('admin.layout')
@section('title','Редактировать производителя')

@section('content')
    <div class="container">
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Редактировать производителя</h1>
                    <p class="mt-1 text-sm text-gray-600">Изменение данных производителя</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.manufacturers') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Назад к списку
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('admin.manufacturers.update', $manufacturer->manufacturer_id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Название производителя <span class="text-red-500">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $manufacturer->name) }}"
                               required
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Описание</label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="10"
                                  class="tinymce-editor @error('description') border-red-300 @enderror">{{ old('description', $manufacturer->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">Веб-сайт</label>
                        <input type="url" 
                               name="website" 
                               id="website" 
                               value="{{ old('website', $manufacturer->website) }}"
                               placeholder="https://example.com"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('website') border-red-300 @enderror">
                        @error('website')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ordering" class="block text-sm font-medium text-gray-700">Порядок сортировки</label>
                        <input type="number" 
                               name="ordering" 
                               id="ordering" 
                               value="{{ old('ordering', $manufacturer->ordering) }}"
                               min="0"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('ordering') border-red-300 @enderror">
                        @error('ordering')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.manufacturers') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Отмена
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts-head')
{{-- TinyMCE: Get free API key at https://www.tiny.cloud/auth/signup/ --}}
<script src="https://cdn.tiny.cloud/1/YOUR_API_KEY_HERE/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
@endpush

@push('scripts')
<script>
    tinymce.init({
        selector: '.tinymce-editor',
        height: 400,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image | code | help',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
        language: 'ru',
        branding: false,
        promotion: false,
        setup: function(editor) {
            editor.on('change', function() {
                editor.save();
            });
        }
    });
</script>
@endpush
