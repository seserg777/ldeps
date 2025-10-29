@props(['product' => null])

<div x-data="productQuickView()">
    <x-modal 
        id="productQuickView" 
        title="Швидкий перегляд товару"
        size="lg"
        :closable="true"
        :backdrop="true"
        :keyboard="true"
    >
        <!-- Loading State -->
        <div x-show="loading" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
            <p class="text-gray-500 mt-2">Завантаження...</p>
        </div>

        <!-- Product Content -->
        <div x-show="!loading && product" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Product Image -->
            <div>
                <img 
                    :src="product ? (product.image || '/images/no-image.svg') : '/images/no-image.svg'" 
                    :alt="product ? product.name : 'Product'"
                    class="w-full h-64 object-cover rounded-lg"
                    @error="$event.target.src = '/images/no-image.svg'"
                >
            </div>
            
            <!-- Product Info -->
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2" x-text="product ? product.name : ''"></h2>
                <p class="text-lg font-semibold text-blue-600 mb-4" x-text="product ? formatPrice(product.price) : ''"></p>
                <p class="text-gray-600 mb-4" x-text="product ? product.description : ''"></p>
                
                <!-- Actions -->
                <div class="flex space-x-3" x-show="product">
                    <button 
                        @click="addToCart(product.product_id)"
                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <i class="fas fa-shopping-cart me-2"></i>Додати до кошика
                    </button>
                    <button 
                        @click="addToWishlist(product.product_id)"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <i class="fas fa-heart me-2"></i>В избранное
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <button 
                    @click="$refs.productQuickView.close()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Закрити
                </button>
                <a 
                    :href="product ? '/products/' + product.product_id : '#'"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    x-show="product"
                >
                    Перейти до товару
                </a>
            </div>
        </x-slot>
    </x-modal>
</div>

<script>
function productQuickView() {
    return {
        loading: false,
        product: null,
        
        async loadProduct(productId) {
            this.loading = true;
            try {
                const response = await fetch(`/api/products/${productId}`);
                this.product = await response.json();
            } catch (error) {
                console.error('Error loading product:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async addToCart(productId) {
            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: productId, quantity: 1 })
                });
                
                if (response.ok) {
                    // Show success message
                    console.log('Product added to cart');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
            }
        },
        
        async addToWishlist(productId) {
            try {
                const response = await fetch('/wishlist/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: productId })
                });
                
                if (response.ok) {
                    console.log('Product added to wishlist');
                }
            } catch (error) {
                console.error('Error adding to wishlist:', error);
            }
        },
        
        formatPrice(price) {
            return new Intl.NumberFormat('uk-UA', {
                style: 'currency',
                currency: 'UAH'
            }).format(price);
        },
        
        open(productId) {
            this.loadProduct(productId);
            this.$refs.productQuickView.open();
        }
    }
}
</script>
