{{--
  Multi-language field component
  
  Usage:
  @include('admin.components.multilang-field', [
      'name' => 'name',
      'label' => 'Product Name',
      'type' => 'text', // text, textarea, tinymce
      'value' => $product,
      'required' => true
  ])
--}}

@php
    $languages = [
        'uk-UA' => ['label' => 'Українська', 'flag' => '🇺🇦', 'code' => 'uk'],
        'ru-UA' => ['label' => 'Русский', 'flag' => '🇷🇺', 'code' => 'ru'],
        'en-GB' => ['label' => 'English', 'flag' => '🇬🇧', 'code' => 'en'],
    ];
    
    $type = $type ?? 'text';
    $required = $required ?? false;
    $value = $value ?? null;
    $rows = $rows ?? 4;
@endphp

<div class="multilang-field" x-data="{ activeTab: 'uk-UA' }">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    
    {{-- Language tabs --}}
    <div class="flex border-b border-gray-200 mb-3">
        @foreach($languages as $lang => $info)
            <button type="button"
                    @click="activeTab = '{{ $lang }}'"
                    :class="activeTab === '{{ $lang }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="py-2 px-4 border-b-2 font-medium text-sm flex items-center space-x-2 transition-colors">
                <span class="text-lg">{{ $info['flag'] }}</span>
                <span>{{ $info['label'] }}</span>
            </button>
        @endforeach
    </div>
    
    {{-- Language inputs --}}
    @foreach($languages as $lang => $info)
        <div x-show="activeTab === '{{ $lang }}'" class="multilang-input">
            @if($type === 'text')
                <input type="text" 
                       name="{{ $name }}_{{ $lang }}" 
                       id="{{ $name }}_{{ $lang }}"
                       value="{{ old($name . '_' . $lang, $value ? $value->{$name . '_' . $lang} : '') }}"
                       {{ $required && $lang === 'uk-UA' ? 'required' : '' }}
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="{{ $label }} ({{ $info['label'] }})">
            
            @elseif($type === 'textarea')
                <textarea name="{{ $name }}_{{ $lang }}" 
                          id="{{ $name }}_{{ $lang }}"
                          rows="{{ $rows }}"
                          {{ $required && $lang === 'uk-UA' ? 'required' : '' }}
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="{{ $label }} ({{ $info['label'] }})">{{ old($name . '_' . $lang, $value ? $value->{$name . '_' . $lang} : '') }}</textarea>
            
            @elseif($type === 'tinymce')
                <textarea name="{{ $name }}_{{ $lang }}" 
                          id="{{ $name }}_{{ $lang }}"
                          rows="{{ $rows }}"
                          class="tinymce-editor tinymce-{{ $name }}-{{ $lang }}"
                          data-lang="{{ $lang }}">{{ old($name . '_' . $lang, $value ? $value->{$name . '_' . $lang} : '') }}</textarea>
            @endif
        </div>
    @endforeach
</div>

