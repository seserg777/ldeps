@props(['name', 'title' => '', 'maxWidth' => '2xl'])

@php
// Map max-width classes
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{ 
        show: false,
        init() {
            console.log('Modal {{ $name }} initialized');
        }
    }"
    x-show="show"
    x-on:open-modal.window="
        console.log('Event received:', $event.detail, 'Expected:', '{{ $name }}');
        if ($event.detail === '{{ $name }}') {
            show = true;
            console.log('Modal {{ $name }} opened');
        }
    "
    x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transform transition-all"
        x-on:click="show = false"
    ></div>

    <!-- Modal -->
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="mb-6 bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto {{ $maxWidthClass }}"
    >
        @if($title)
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        </div>
        @endif

        <div class="px-6 py-4">
            {{ $slot }}
        </div>
    </div>
</div>

