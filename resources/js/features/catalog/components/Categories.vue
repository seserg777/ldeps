<template>
  <div class="categories-container">
    <div v-if="loading" class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Завантаження...</span>
      </div>
    </div>
    
    <div v-else-if="error" class="alert alert-danger">
      <i class="fas fa-exclamation-triangle me-2"></i>
      {{ error }}
    </div>
    
    <div v-else class="row">
      <div v-for="category in categories" :key="category.category_id" class="col-12 mb-4">
        <Category 
          :category="category"
          :products="category.products || []"
          @add-to-cart="handleAddToCart"
          @add-to-wishlist="handleAddToWishlist"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import Category from './Category.vue'

const props = defineProps({
  categories: { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
  error: { type: String, default: null }
})

const emit = defineEmits(['add-to-cart', 'add-to-wishlist'])

const handleAddToCart = (productId) => emit('add-to-cart', productId)
const handleAddToWishlist = (productId) => emit('add-to-wishlist', productId)
</script>

<style scoped>
.categories-container { min-height: 200px; }
</style>


