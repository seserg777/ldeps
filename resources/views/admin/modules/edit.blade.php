@extends('admin.layout')

@section('title', 'Edit Module')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.modules.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Module: {{ $module->title }}</h1>
    </div>
</div>

@if (session('error'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.modules.update', $module->id) }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Title -->
        <div class="md:col-span-2">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                Title <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="title" 
                   id="title" 
                   value="{{ old('title', $module->title) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
        </div>

        <!-- Note -->
        <div class="md:col-span-2">
            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">
                Note
            </label>
            <input type="text" 
                   name="note" 
                   id="note" 
                   value="{{ old('note', $module->note) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Short description or note">
        </div>

        <!-- Position -->
        <div>
            <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                Position <span class="text-red-500">*</span>
            </label>
            <select name="position" 
                    id="position" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                <option value="">Select Position</option>
                @foreach($positions as $key => $label)
                    <option value="{{ $key }}" {{ old('position', $module->position) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Module Type -->
        <div>
            <label for="module" class="block text-sm font-medium text-gray-700 mb-2">
                Module Type
            </label>
            <input type="text" 
                   name="module" 
                   id="module" 
                   value="{{ old('module', $module->module) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="e.g., mod_custom, mod_menu">
        </div>

        <!-- Ordering -->
        <div>
            <label for="ordering" class="block text-sm font-medium text-gray-700 mb-2">
                Ordering
            </label>
            <input type="number" 
                   name="ordering" 
                   id="ordering" 
                   value="{{ old('ordering', $module->ordering) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Language -->
        <div>
            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                Language
            </label>
            <input type="text" 
                   name="language" 
                   id="language" 
                   value="{{ old('language', $module->language) }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="* for all languages">
        </div>

        <!-- Published -->
        <div class="flex items-center space-x-4">
            <div class="flex items-center">
                <input type="checkbox" 
                       name="published" 
                       id="published" 
                       value="1"
                       {{ old('published', $module->published) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="published" class="ml-2 block text-sm text-gray-900">
                    Published
                </label>
            </div>
            
            <div class="flex items-center">
                <input type="checkbox" 
                       name="showtitle" 
                       id="showtitle" 
                       value="1"
                       {{ old('showtitle', $module->showtitle) ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="showtitle" class="ml-2 block text-sm text-gray-900">
                    Show Title
                </label>
            </div>
        </div>

        <!-- Content -->
        <div class="md:col-span-2">
            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                Content
            </label>
            <textarea name="content" 
                      id="content" 
                      rows="6"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('content', $module->content) }}</textarea>
        </div>

        <!-- Menu Items -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Assign to Menu Items
            </label>
            <div class="border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto">
                @if($menuItems->count() > 0)
                    <div class="space-y-2">
                        @php
                            $selectedMenuIds = old('menu_items', $module->menuItems->pluck('id')->toArray());
                        @endphp
                        @foreach($menuItems as $menuItem)
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="menu_items[]" 
                                       id="menu_{{ $menuItem->id }}" 
                                       value="{{ $menuItem->id }}"
                                       {{ in_array($menuItem->id, $selectedMenuIds) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="menu_{{ $menuItem->id }}" class="ml-2 block text-sm text-gray-900">
                                    {{ $menuItem->title }}
                                    @if($menuItem->menutype)
                                        <span class="text-xs text-gray-500">({{ $menuItem->menutype }})</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No menu items available</p>
                @endif
            </div>
            @if($module->menuItems->count() > 0)
                <div class="mt-2 text-sm text-gray-600">
                    <strong>Currently assigned to:</strong>
                    @foreach($module->menuItems as $menuItem)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-blue-100 text-blue-800 mr-1">
                            {{ $menuItem->title }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Params (JSON) -->
        <div class="md:col-span-2">
            <label for="params" class="block text-sm font-medium text-gray-700 mb-2">
                Parameters (JSON)
            </label>
            <textarea name="params" 
                      id="params" 
                      rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                      placeholder='{"key": "value"}'>{{ old('params', $module->params) }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex justify-between">
        <form action="{{ route('admin.modules.destroy', $module->id) }}" 
              method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this module?');">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                <i class="fas fa-trash mr-2"></i>
                Delete Module
            </button>
        </form>
        
        <div class="flex space-x-3">
            <a href="{{ route('admin.modules.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
                <i class="fas fa-save mr-2"></i>
                Update Module
            </button>
        </div>
    </div>
</form>
@endsection

