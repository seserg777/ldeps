<template>
  <div class="product-list">
    <div v-if="showCategoryHeader && category" class="product-list-header mb-4">
      <h3 class="product-list-title">
        <i class="fas fa-th-large me-2"></i>
        {{ category.name }}
      </h3>
    </div>

    <div v-if="loading" class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Завантаження товарів...</span>
      </div>
    </div>
    <div v-else-if="error" class="alert alert-danger">
      <i class="fas fa-exclamation-triangle me-2"></i>
      {{ error }}
    </div>
    <div v-else-if="products.length > 0" class="row">
      <div v-for="product in products" :key="product.product_id" class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <Product :product="product" @add-to-cart="handleAddToCart" @add-to-wishlist="handleAddToWishlist" />
      </div>
    </div>
    <div v-else class="empty-products text-center py-5">
      <i class="fas fa-search fa-3x text-muted mb-3"></i>
      <h4 class="text-muted">Товари не знайдені</h4>
      <p class="text-muted">Спробуйте змінити параметри пошуку або фільтри</p>
    </div>
  </div>
</template>

<script setup>
import Product from './Product.vue'

const props = defineProps({
  products: { type: Array, default: () => [] },
  category: { type: Object, default: null },
  showCategoryHeader: { type: Boolean, default: true },
  loading: { type: Boolean, default: false },
  error: { type: String, default: null },
})

const emit = defineEmits(['add-to-cart', 'add-to-wishlist'])

const handleAddToCart = (productId) => emit('add-to-cart', productId)
const handleAddToWishlist = (productId) => emit('add-to-wishlist', productId)
</script>

<style scoped>
.product-list { min-height: 200px; }
.product-list-header { border-bottom: 1px solid #e9ecef; padding-bottom: 1rem; }
.product-list-title { color: #2c3e50; font-weight: 600; margin-bottom: 0; }
.empty-products { background: #f8f9fa; border-radius: 8px; border: 2px dashed #dee2e6; }
</style>


