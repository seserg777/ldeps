<template>
  <div class="position-relative overflow-hidden">
    <img :src="src" class="card-img-top product-image lazy" :alt="alt" loading="lazy" width="300" height="200" decoding="async" />
    <div class="product-badges">
      <span v-if="hits > 100" class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">
        <i class="fas fa-fire me-1"></i>Хіт
      </span>
      <span v-if="isSale" class="badge bg-success position-absolute top-0 end-0 m-2">
        <i class="fas fa-percent me-1"></i>Акція
      </span>
    </div>
    <div class="product-overlay">
      <div class="overlay-actions">
        <a :href="url" class="btn btn-light btn-sm rounded-circle me-2" title="Швидкий перегляд">
          <i class="fas fa-eye"></i>
        </a>
        <button class="btn btn-danger btn-sm rounded-circle me-2 add-to-wishlist" :data-product-id="productId" title="Додати до бажань" @click="$emit('wishlist')">
          <i class="fas fa-heart"></i>
        </button>
        <button class="btn btn-primary btn-sm rounded-circle add-to-cart" :data-product-id="productId" title="Додати до кошика" @click="$emit('cart')">
          <i class="fas fa-shopping-cart"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  src: { type: String, required: true },
  alt: { type: String, default: '' },
  url: { type: String, required: true },
  productId: { type: [Number, String], required: true },
  hits: { type: Number, default: 0 },
  isSale: { type: Boolean, default: false }
})
</script>


