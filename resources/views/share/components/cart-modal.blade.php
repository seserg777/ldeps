@props(['cartData' => []])

<div x-data="cartModal()" x-show="isOpen" class="fixed inset-0 z-50 bg-black bg-opacity-50" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <span x-text="'Кошик (' + cartCount + ')'"></span>
                </h3>
                <button 
                    @click="close()"
                    class="text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-full p-1"
                >
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-4">
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
                <div x-show="!loading && cartItems.length > 0" class="space-y-4 max-h-96 overflow-y-auto">
                    <template x-for="item in cartItems" :key="item.product_id">
                        <div class="flex items-center space-x-4 py-3 border-b border-gray-100 last:border-b-0">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                <img 
                                    :src="item.image || '/images/no-image.svg'" 
                                    :alt="item.name"
                                    class="w-16 h-16 object-cover rounded-lg"
                                    @error="$event.target.src = '/images/no-image.svg'"
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
                <div class="flex justify-end">
                    <a 
                        href="{{ route('cart.index') }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-center"
                    >
                        Перейти до кошика
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>