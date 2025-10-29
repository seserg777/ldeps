@props([
    'id' => 'modal',
    'title' => 'Modal Title',
    'size' => 'md', // sm, md, lg, xl
    'closable' => true,
    'backdrop' => true,
    'keyboard' => true
])

<div 
    x-data="modalComponent('{{ $id }}', {{ $backdrop ? 'true' : 'false' }}, {{ $keyboard ? 'true' : 'false' }})"
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    @keydown.escape.window="close()"
    @click.self="close()"
>
    <!-- Backdrop -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50"
    ></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="relative w-full max-w-{{ $size }} transform overflow-hidden rounded-lg bg-white shadow-xl"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $title }}
                </h3>
                @if($closable)
                    <button 
                        @click="close()"
                        class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full p-1"
                    >
                        <i class="fas fa-times text-xl"></i>
                    </button>
                @endif
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @if(isset($footer))
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function modalComponent(modalId, backdropClose = true, keyboardClose = true) {
    return {
        isOpen: false,
        
        init() {
            // Listen for open events
            this.$watch('isOpen', (value) => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        },
        
        open() {
            this.isOpen = true;
            this.$nextTick(() => {
                // Focus first focusable element
                const focusable = this.$el.querySelector('input, button, select, textarea, [tabindex]:not([tabindex="-1"])');
                if (focusable) {
                    focusable.focus();
                }
            });
        },
        
        close() {
            this.isOpen = false;
        },
        
        toggle() {
            this.isOpen = !this.isOpen;
        }
    }
}
</script>
