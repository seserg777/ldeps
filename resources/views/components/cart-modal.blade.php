@props(['cartData' => []])

<div 
    x-data="cartModal()"
    x-init="loadCart()"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    x-show="isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
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
            class="relative w-full max-w-md transform overflow-hidden rounded-lg bg-white shadow-xl"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Кошик (<span x-text="cartCount"></span>)
                </h3>
                <button 
                    @click="close()"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full p-1"
                >
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4 max-h-96 overflow-y-auto">
                <!-- Loading State -->
                <div x-show="loading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                    <p class="text-gray-500 mt-2">Завантаження...</p>
                </div>

                <!-- Empty Cart -->
                <div x-show="!loading && cartItems.length === 0" class="text-center py-8">
                    <i class="fas fa-shopping-cart fa-3x text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Кошик порожній</p>
                    <p class="text-sm text-gray-400">Додайте товари до кошика</p>
                </div>

                <!-- Cart Items -->
                <div x-show="!loading && cartItems.length > 0" class="space-y-4">
                    <template x-for="item in cartItems" :key="item.product_id">
                        <div class="flex items-center space-x-4 py-3 border-b border-gray-100 last:border-b-0">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                <img 
                                    :src="item.image || '/images/no-image.svg'" 
                                    :alt="item.name"
                                    class="w-16 h-16 object-cover rounded-lg"
                                >
                            </div>
                            
                            <!-- Product Info -->
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate" x-text="item.name"></h4>
                                <p class="text-sm text-gray-500">Кількість: <span x-text="item.quantity"></span></p>
                                <p class="text-sm font-semibold text-blue-600" x-text="formatPrice(item.price)"></p>
                            </div>
                            
                            <!-- Remove Button -->
                            <button 
                                @click="removeItem(item.product_id)"
                                class="text-red-400 hover:text-red-600 p-1"
                                title="Видалити з кошика"
                            >
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer -->
            <div x-show="!loading && cartItems.length > 0" class="border-t border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-lg font-semibold">Всього:</span>
                    <span class="text-lg font-bold text-blue-600" x-text="formatPrice(totalPrice)"></span>
                </div>
                <div class="flex space-x-3">
                    <button 
                        @click="close()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Продовжити покупки
                    </button>
                    <a 
                        href="{{ route('cart.index') }}"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-center"
                    >
                        Перейти до кошика
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function cartModal() {
    return {
        isOpen: false,
        loading: false,
        cartItems: [],
        cartCount: 0,
        totalPrice: 0,
        
        init() {
            this.$watch('isOpen', (value) => {
                if (value) {
                    document.body.style.overflow = 'hidden';
                    this.loadCart();
                } else {
                    document.body.style.overflow = '';
                }
            });
        },
        
        async loadCart() {
            this.loading = true;
            try {
                const response = await fetch('{{ route("cart.modal") }}');
                const data = await response.json();
                
                this.cartItems = data.items || [];
                this.cartCount = data.count || 0;
                this.totalPrice = data.total || 0;
            } catch (error) {
                console.error('Error loading cart:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async removeItem(productId) {
            try {
                const response = await fetch('{{ route("cart.remove") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: productId })
                });
                
                if (response.ok) {
                    await this.loadCart();
                    // Update cart count in header
                    this.updateHeaderCartCount();
                }
            } catch (error) {
                console.error('Error removing item:', error);
            }
        },
        
        updateHeaderCartCount() {
            // Update the cart count in the header
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = this.cartCount;
            }
        },
        
        formatPrice(price) {
            return new Intl.NumberFormat('uk-UA', {
                style: 'currency',
                currency: 'UAH'
            }).format(price);
        },
        
        open() {
            this.isOpen = true;
        },
        
        close() {
            this.isOpen = false;
        }
    }
}
</script>
