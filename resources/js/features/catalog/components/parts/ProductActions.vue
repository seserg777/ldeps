<template>
  <div class="btn-group" role="group">
    <a :href="url" class="btn btn-outline-primary btn-sm">
      <i class="fas fa-info-circle me-1"></i>Деталі
    </a>
    <button class="btn btn-outline-danger btn-sm add-to-wishlist" :data-product-id="productId" @click="$emit('wishlist')">
      <i class="fas fa-heart me-1"></i>Бажання
    </button>
    <button class="btn btn-primary btn-sm add-to-cart" :data-product-id="productId" @click="$emit('cart')">
      <i class="fas fa-cart-plus me-1"></i>В кошик
    </button>
  </div>
</template>

<script setup>
defineProps({
  productId: { type: [Number, String], required: true },
  url: { type: String, required: true }
})
</script>


