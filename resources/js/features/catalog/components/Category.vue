<template>
  <div class="category-section">
    <div class="category-header mb-4">
      <h2 class="category-title">
        <i class="fas fa-folder me-2"></i>
        {{ category.name }}
      </h2>
      <p v-if="category.description" class="category-description text-muted">
        {{ category.description }}
      </p>
      <div class="category-meta">
        <span class="badge bg-primary me-2">
          <i class="fas fa-box me-1"></i>
          {{ products.length }} товарів
        </span>
        <a v-if="category.url" :href="category.url" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-eye me-1"></i>
          Переглянути всі
        </a>
      </div>
    </div>

    <div v-if="products.length > 0" class="category-products">
      <ProductList :products="products" :show-category-header="false" @add-to-cart="handleAddToCart" @add-to-wishlist="handleAddToWishlist" />
    </div>

    <div v-else class="empty-category text-center py-5">
      <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
      <h4 class="text-muted">Товари відсутні</h4>
      <p class="text-muted">У цій категорії поки немає товарів</p>
    </div>
  </div>
</template>

<script setup>
import ProductList from './ProductList.vue'

const props = defineProps({
  category: { type: Object, required: true },
  products: { type: Array, default: () => [] }
})

const emit = defineEmits(['add-to-cart', 'add-to-wishlist'])

const handleAddToCart = (productId) => emit('add-to-cart', productId)
const handleAddToWishlist = (productId) => emit('add-to-wishlist', productId)
</script>

<style scoped>
.category-section { background: #fff; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.category-header { border-bottom: 1px solid #e9ecef; padding-bottom: 1rem; }
.category-title { color: #2c3e50; font-weight: 600; margin-bottom: 0.5rem; }
.category-meta { display: flex; align-items: center; gap: 0.5rem; }
.empty-category { background: #f8f9fa; border-radius: 8px; border: 2px dashed #dee2e6; }
</style>


